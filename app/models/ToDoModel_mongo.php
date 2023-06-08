<?php

// include Composer's autoloader
require VENDOR_PATH.'autoload.php'; 

/**
 * ToDo model for the application.
 * Handles access to MongoDB Data Base.
 */
class ToDoModel_mongo implements ToDoModelInterface {

    protected $_dbh = null;
    protected $_collection = "";

    public function __construct() {
		// parses the settings file
		$settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
		
		// starts the connection to the database
        $this->_dbh = new MongoDB\Client('mongodb://localhost:27017');
		
        #$this->_collection = $this->_dbh->$settings['mongodb']['dbname']->$settings['mongodb']['collection'];
        $this->_collection = $this->_dbh->todo->tasks;
	}


    ################################################
    # CRUD: CLASS METHODS TO OPERATE WITH DATABASE #    
    ################################################

    // CREATE: method that creates a task and adds it to the MongoDB DataBase
    public function createTask(string $name, string $author): bool | string {

        // get all tasks
        $tasks = $this->getTasks();

        // get highest id in DB to determine new id
        $highestId = -1; // Initialize with a value lower than any possible id
        foreach ($tasks as $task) {
            if ($task['id'] > $highestId) {
                $highestId = $task['id'];
            }
        }
        $newId = $highestId + 1;

        // build next taks
        $new_task['id'] = $newId;
        $new_task['name'] = $name;
        $new_task['status'] = 'Pending';
        $new_task['start_time'] = null;
        $new_task['end_time'] = null;
        $new_task['author'] = $author;

        $result = $this->_collection->insertOne( $new_task );

        if (!$result->getInsertedCount()){
            return "MongoDB insert failed";
        }
        return true;
    }

    // READ: method that returns an array containing all tasks in MongoDB DataBase
    public function getTasks(): array | string {

        // MongoDB query
        $result = $this->_collection->find([]);

        // error handling
        if ($result instanceof MongoDB\Driver\Cursor) {
            // Query executed successfully
            
            // Check if any documents were found
            if ($result->isDead()) {
                // No documents found
                return "GetTasks-Model: MongoDB is empty";
            } else {
                // Documents found
                // Convert result into array
                $tasks =json_decode(json_encode($result->toArray(),true), true);
                return $tasks;
            }
        } else {
            // Query execution failed
            return "GetTasks-Model: MongoDB not reachable.";
        }
    }

    // READ: method that gets a task by its 'id' from MongoDB DataBase
    public function getTaskById(int $id): array | string {
        // returns one task by its $id
        // MongoDB query
        $result = $this->_collection->find(['id' => (int) $id]);
        
        // error handling
        if ($result instanceof MongoDB\Driver\Cursor) {
            // Query executed successfully
            
            // Check if document was found
            if ($result->isDead()) {
                // No document found
                return "GetTaskById-Model: 'id' not found in MongoDB.";
            } else {
                // Document found
                // Convert result into array and return it
                return json_decode(json_encode($result->toArray(),true), true)[0];
            }
        } else {
            // Query execution failed
            return "GetTaskById-Model: MongoDB not reachable.";
        }
    }
    
    // UPDATE: method that updates a task in MongoDB DataBase
    public function updateTask($data, $id): bool | string {
        // https://www.mongodb.com/developer/languages/php/php-crud/#updating-documents-with-php
        
        $original_task = $this->getTaskById($id);
        // error handling
        if (is_string($original_task)){
            return "UpdateTask-Model: ".$original_task;
        }

        // si ha canviat l'estat a 'finished', posarem 'end_time' a la data/hora del canvi
        if ( $data['status'] == 'Finished' && $original_task['status'] != 'Finished'){
            $original_task['end_time'] = date("Y-m-d H:i:s", time());
        }
        // si ha canviat l'estat a 'Ongoing' posarem 'start_date' a l'hora del canvi i posarem 'end_time'en NULL
        elseif ( $data['status'] == 'Ongoing' && $original_task['status'] != 'Ongoing'){
            $original_task['start_time'] = date("Y-m-d H:i:s", time());
            $original_task['end_time'] = null;
        }
        // si ha canviat l'estat a 'Pending' , posarem 'start/end_time' en NULL
        elseif ( $data['status'] == 'Pending' && $original_task['status'] != 'Pending'){
            $original_task['start_time'] = null;
            $original_task['end_time'] = null;
        }

        // create updated task
        $updated_task = array_merge($original_task, $data);

        // if original task has been modified, we update the doc in the db
        if ($updated_task != $original_task){
            $result = $this->_collection->updateOne(['id' => (int) $id], [ '$set' => array_slice($updated_task, 2)] );

            // error handling
            if ($result instanceof MongoDB\UpdateResult ) {
                // Query executed successfully
                
                // Check if document was updated
                if (!$result->getModifiedCount()) {
                    // No documents updated
                    return "UpdateTask-Model: MongoDB update failed.";
                } else {
                    // Document updated
                    return true;
                }
            } else {
                // Query execution failed
                return "UpdateTask-Model: MongoDB not reachable.";
            }

        }else {
            // no modifications made. Leave without updating db
            return "UpdateTask-Model: no changes found in your request. No updates made in MongoDB.";
        }
    }


    // DELETE: method that deletes a task from Mongo DataBase
    public function deleteTask(int $id): bool {

        $task = $this -> getTaskById($id);

        if(!$task) {
            return false;  
        } 

        $result =  $this->_collection->deleteOne(['id' => $id]); 

        if (!$result->getDeletedCount()) { 
            return false;   
        }
        
        return true;      
    }
}