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

        return $this->save($new_task);
    }

    // READ: method that returns an array containing all tasks in MySQL DataBase
    public function getTasks(): array | string {

        // SQL query
        $sql = 'select * from ' . $this->_table;
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        // store all returned rows in array of stdClass objects
        $result = $statement->fetchAll(PDO::FETCH_OBJ);

        // Convert stdClass objects to associative arrays
        $tasks = json_decode(json_encode($result), true);
        
        return $tasks;
    }
    
    // READ: method that gets a task by its 'id' from MySQL DataBase
    public function getTaskById($id){

        // returns one task by its $id
        $result = $this->fetchOne($id);

        // Convert stdClass objects to associative arrays
        $task = json_decode(json_encode($result), true);

        return $task;
    }

    // UPDATE: method that updates a task and saves the changes
    public function updateTask(array $data, int $id): bool {
        
        $original_task = $this->getTaskById($id);

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

        // updating task with new data
        $updated_task = array_merge($original_task, $data);

        return $this->save($updated_task);
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