<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ToDoController extends Controller 
{
	
    ###############################################
    # HOME:                                       #
    ###############################################
    // home method that will show main menu
    public function indexAction(){
    }


    ################################################
    # CRUD: CONTROLLER METHODS                     #    
    ################################################

    // CREATE TASK
    public function createTaskAction(){
        if (($_SERVER['REQUEST_METHOD'] == 'POST') &&  (!empty($_POST["name"])) && (!empty($_POST["author"]))) {

            //Get new data from user
            $name = $_POST['name'];
            $author = $_POST['author'];

            $todo = $this-> setModel();
            
            //Snd them to model to create task
            $result = $todo -> createTask($name, $author);
            
            if (is_string($result)){
                throw new Exception("CreateTask: ".$result);

            } else {
                // redirect to tasks list
                header("Location:showAll");
                exit;
            }
        } 
    }
    
    // READ TASK
    public function showTaskAction(){

        if (isset($_GET['id'])) {
            
            $taskId = $_GET['id'];

            $todo = $this-> setModel();
            
            $task = $todo->getTaskById($taskId);
            // error handling
            if(is_string($task)) {
                throw new Exception($task);
            }  
            $this->view->task = $task;

        } 
    }

    // READ ALL TASKS
	public function showAllTasksAction(){
        if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $_SESSION['db_type']= $_POST['db_type'];
        }

        $todo = $this-> setModel();
        $tasks = $todo->getTasks();
        // error handling
        if(is_string($tasks)) {
            throw new Exception("ShowAllTasks: ".$tasks);  //$tasks will contain the error msg from Model
        }
        $this->view->tasks = $tasks;
    }

	
    // SORT TASKS BY FIELD IN ASC ORDER
	public function sortTasksAction(){

        if(isset($_GET['sortBy'])) {

            //receiving and decoding sorting field and order type (asc / desc)
            (array) $sortBy = explode(",", $_GET['sortBy']);

            (Object) $todo = $this-> setModel();
            $tasks = $todo->getTasks();

            // function that orders alphabetically an array of tasks 
            // by a given field either in asc or desc order
            usort($tasks, function ($a, $b) use ($sortBy) {
                $keyA = $a[$sortBy[0]] ?? '';
                $keyB = $b[$sortBy[0]] ?? '';
                
                if ( $sortBy[1] == "asc"){
                    return strcmp($keyA, $keyB);
                } else {
                    return strcmp($keyB, $keyA);
                }
            });

            $this->view->tasks = $tasks;

        }
    }


    // UPDATE TASK
    public function updateTaskAction(){

        $this -> showTaskAction();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ((!empty($_POST["name"])) && (!empty($_POST["author"]))) {

                $taskId = $_GET['id'];

                // receiving new data
                $newData['name']   = $_POST['name'];
                $newData['status'] = $_POST['status'];
                $newData['author'] = $_POST['author'];

                // sending new data to Model 
                $todo = $this-> setModel();

                $result = $todo -> updateTask($newData, $taskId);
                // error handling
                if (is_string($result)){
                    throw new Exception ("UpdateTask: ".$result);
                }

                // redirecting to tasks list
                header("Location: showAll");
                exit;
                

            } elseif((empty($_POST["name"])) OR (empty($_POST["author"]))) {

                throw new Exception("UpdateTask: Task Name and Author fields are required.");

            }

        }


    }

    // DELETE TASK
	public function deleteTaskAction(){

        if(isset($_GET['id'])) {

            $taskId = $_GET['id'];

            $todo = $this-> setModel();

            $result = $todo -> deleteTask($taskId);

            if (!$result){
                throw new Exception("Delete failed.");

            } else {
                // redirecting to tasks list
                header("Location: showAll");
                exit;
            }

        } else {
            throw new Exception("Not found.");
        } 
        
    }

    ###############################################
    # HELPER FUNCTIONS                            #
    ###############################################

    // helper function
    public function setModel(): Object {
        switch ($_SESSION['db_type']){

            case "json":
                return new ToDoModel_json();
            case "mysql":
                return new ToDoModel_mysql();
            case "mongodb":
                return new ToDoModel_mongo();
            default:
            throw new Exception("Wrong DataBase!");
        }
    }

}