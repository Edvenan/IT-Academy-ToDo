<?php

/**
 * ToDo model for the application.
 * Handles access to JSON Data Base.
 */

class ToDoModel_json implements ToDoModelInterface{

    // set the json file we want to look into
    protected $json_file = ROOT_PATH.'/app/models/data/json/data.json';


    ################################################
    # CRUD: CLASS METHODS TO OPERATE WITH DATABASE #    
    ################################################

    // CREATE: method that creates a task and adds it to the array of tasks
    public function createTask(string $name, string $author): bool | string {

        // get all tasks
        $tasks = $this->getTasks();
        // error handling
        if( (is_string($tasks)) && ($tasks != "json DataBase is empty") ){
            return ("CreateTask-Model: ".$tasks); // return error msg from getTasks()
        }

        if (is_string($tasks)) {
            // if tasks is a string (DataBase is empty),
            // new taskId = 1
            $new_taskId = 1;
            // initialise $tasks array
            $tasks = [];
        } else {
            // new taskId =  last taskId + 1
            $new_taskId = end($tasks)['id'] + 1;
        }

        $new_task['id'] = $new_taskId;
        $new_task['name'] = $name;
        $new_task['start_time'] = null;
        $new_task['end_time'] = null;
        $new_task['author'] = $author;
        $new_task['status'] = 'Pending';

        // add the new task object to all tasks
        $tasks[] = $new_task;

        $result = $this->saveTasks($tasks);
        // error handling
        if(is_string($result)){
            return ("CreateTask-Model: ".$result);
        }

        return true;
    }
    
    // READ: method that returns an array containing all tasks
    public function getTasks(): array |string {

        // read json database content
        $jsonFile = file_get_contents($this->json_file);
        // error handling
        if (!is_string($jsonFile)){
            return ("GetTasks-Model: json DataBase not reachable.");
        }
        
        // convert contents to associative array of tasks
        (array) $tasks = json_decode($jsonFile, true);  
        // error handling
        if(!$tasks){
            return ("json DataBase is empty");
        }

        return $tasks;
    }

    // READ: method that gets a task by its 'id' from 'db_type' DataBase 
    public function getTaskById($id): array | string {

        // get all tasks
        $tasks = $this->getTasks();
        // error handling
        if( (is_string($tasks)) && ($tasks != "json DataBase is empty") ){
            return ("GetTaskById-Model: ".$tasks); // return error msg from getTasks()
        }

        foreach($tasks as $task) {
            if ($task['id'] == $id) {
              return $task;
            }
        }
        // handling id not found
        return ("GetTaskById-Model: id not found in json DataBase.");
    }

    // UPDATE: method that updates a task and the array of tasks
    public function updateTask(array $data, int $id): bool | string {

        $tasks = $this->getTasks();
        // error handling
        if( (is_string($tasks)) ){
            return ("UpdateTask-Model: ".$tasks); // return error msg from getTasks()
        }


        foreach($tasks as $i => $task) {

            if($task['id'] == $id) {

                //if status has changed to 'Ongoing', sets 'start_time': current date and time & 'end_time': NULL
                if ($data['status'] == 'Ongoing' && $tasks[$i]['status']  != 'Ongoing') {
                    $tasks[$i]['start_time'] = date("Y-m-d H:i:s", time());
                    $tasks[$i]['end_time'] = null;
                }
                // if status has changed to 'Finished' from 'Ongoing', sets 'end_time': current date and time
                elseif ( $data['status'] == 'Finished' && $tasks[$i]['status'] == 'Ongoing'){
                    $tasks[$i]['end_time'] = date("Y-m-d H:i:s", time());
                }
                // if status has changed to 'Finished' from 'Pending', sets 'start/end_time': current date and time
                elseif ( $data['status'] == 'Finished' && $tasks[$i]['status'] == 'Pending'){
                    $tasks[$i]['start_time'] = date("Y-m-d H:i:s", time());
                    $tasks[$i]['end_time'] = date("Y-m-d H:i:s", time());
                }
                // if status has changed to 'Pending', sets 'start/end_time': NULL
                elseif ( $data['status'] == 'Pending' && $tasks[$i]['status'] != 'Pending'){
                    $tasks[$i]['start_time'] = null;
                    $tasks[$i]['end_time'] = null;
                }
               
                if ($tasks[$i] == array_merge($tasks[$i], $data)){
                    // no modifications made. Leave without updating db
                    return "UpdateTask-Model: no changes found in your request. No update made into json DataBase.";
                } 


               // updating task with new data
               $tasks[$i] = array_merge($tasks[$i], $data);

               $result = $this -> saveTasks($tasks); 
                // error handling
                if(is_string($result)){
                    return ("UpdateTask-Model: ".$result);
                }
                return $result;
        
            }
             
        } 
        return "UpdateTask-Model: Couldn't find this task in Json files";
    }

    // DELETE: method that deletes a task from the DataBase
    public function deleteTask(int $id): bool | string {
        
        $tasks = $this->getTasks();
        // error handling
        if((is_string($tasks)) && ($tasks != "DataBase is empty") ){
            return ("DeleteTask-Model: ".$tasks); // return error msg from getTasks()
        }

        // delete variable
        if (count($tasks) == 1 && $tasks[0]['id']==$id){
            $tasks = "";
        } else {
            foreach ($tasks as $k => $task){
                if ($task['id'] == $id) {
                    unset($tasks[$k]);
                    break;
                }
            }
        }

        $result =  $this->saveTasks($tasks);
        // error handling
        if(is_string($result)){
            return ("DeleteTask-Model: ".$result);
        }

        return true;

    }

    ###############################################
    # HELPER FUNCTIONS                            #    
    ###############################################
    
    // method that saves an array containing all tasks to a json DataBase
    // after a task update or a create new task
    public function saveTasks(array $tasks): bool | string {

        foreach($tasks as $k => $task){
            $tasks_json[] = $task;
        }

        // save all tasks to json - repasar!
        $result = file_put_contents(ROOT_PATH.'/app/models/data/json/data.json', json_encode($tasks_json, JSON_PRETTY_PRINT));
        if (!$result){
            return "json update/insert failed";
        }
        return true;
    }
}