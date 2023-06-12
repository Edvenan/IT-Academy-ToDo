<?php

/**
 * ToDo model for the application.
 * Handles access to MySQL Data Base.
 */
class ToDoModel_mysql extends Model implements ToDoModelInterface {

    // set the table we want to look into
    protected $_table = 'tasks';

    ################################################
    # CRUD: CLASS METHODS TO OPERATE WITH DATABASE #    
    ################################################

    // CREATE: method that creates a task and adds it to the MySQL DataBase
    public function createTask(string $name, string $author): bool | string {

        $new_task['name'] = $name;
        $new_task['start_time'] = null;
        $new_task['end_time'] = null;
        $new_task['author'] = $author;
        $new_task['status'] = 'Pending';

        $result = $this->save($new_task);
        // error handling
        if(!$result){
            return ("CreateTask-Model: Save(): MySQL insert failed");
        }

        return true;
    }

    // READ: method that returns an array containing all tasks in MySQL DataBase
    public function getTasks(): array | string {

        // SQL query
        $sql = 'select * from ' . $this->_table;
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        //error handling
        if (!$statement){
            return ("GetTasks-Model: MySQL DataBase not reachable.");
        }

        // store all returned rows in array of stdClass objects
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        //error handling
        if (!$result){
            return ("GetTasks-Model: MySQL DataBase is empty");
        }

        // Convert stdClass objects to associative arrays
        $tasks = json_decode(json_encode($result), true);
        
        return $tasks;
    }

    // READ: method that gets a task by its 'id' from MySQL DataBase
    public function getTaskById($id): array | string {

        // returns one task by its $id
        $result = $this->fetchOne($id);
        // error handling
        if (!$result){
            return "GetTaskById-Model: id not found in MySQL DataBase.";
        }

        // Convert stdClass objects to associative arrays
        $task = json_decode(json_encode($result), true);

        return $task;
    }

     // UPDATE: method that updates a task in MySQL DataBase
     public function updateTask($data, $id): bool | string {
        
        $original_task = $this->getTaskById($id);
        // error handling
        if (is_string($original_task)){
            return "UpdateTask-Model: ".$original_task;
        }

        //if status has changed to 'Ongoing', sets 'start_time': current date and time & 'end_time': NULL
        if ($data['status'] == 'Ongoing' && $original_task['status']  != 'Ongoing') {
            $original_task['start_time'] = date("Y-m-d H:i:s", time());
            $original_task['end_time'] = null;
        }
        // if status has changed to 'Finished' from 'Ongoing', sets 'end_time': current date and time
        elseif ( $data['status'] == 'Finished' && $original_task['status'] == 'Ongoing'){
            $original_task['end_time'] = date("Y-m-d H:i:s", time());
        }
        // if status has changed to 'Finished' from 'Pending', sets 'start/end_time': current date and time
        elseif ( $data['status'] == 'Finished' && $original_task['status'] == 'Pending'){
            $original_task['start_time'] = date("Y-m-d H:i:s", time());
            $original_task['end_time'] = date("Y-m-d H:i:s", time());
        }
        // if status has changed to 'Pending', sets 'start/end_time': NULL
        elseif ( $data['status'] == 'Pending' && $original_task['status'] != 'Pending'){
            $original_task['start_time'] = null;
            $original_task['end_time'] = null;
        }

        $updated_task = array_merge($original_task, $data);

        // if original task has been modified, we update the doc in the db
        if ($updated_task != $original_task){

            $result = $this->save($updated_task);
            // error handling
            if(!$result){
                return ("UpdateTask-Model: Save(): MySQL set failed");
            }
            return true;
        }else {
            // no modifications made. Leave without updating db
            return "UpdateTask-Model: no changes found in your request. No update made into MySQL.";
        }
    }
    
    // DELETE: method that deletes a task from MySQL DataBase
    public function deleteTask(int $id): bool {

        $task = $this -> getTaskById($id);

        if(!$task) {
            return false;  
        }  

        return $this->delete($id);

    } 
    
}