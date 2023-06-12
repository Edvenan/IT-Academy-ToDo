<?php

/**
 * ToDo model interface for the application.
 */
interface ToDoModelInterface 
{
    ################################################
    # CRUD: CLASS METHODS TO OPERATE WITH DATABASE #    
    ################################################

    // CREATE: method that creates a task and adds it to the DataBase
    public function createTask(string $name, string $author): bool| string; 

    // READ: method that returns an array containing all tasks from the DataBase
    public function getTasks(): array | string;

    // READ: method that gets a task by its 'id' from the DataBase
    public function getTaskById(int $id): array | string;

    // UPDATE: method that updates a task in the DataBase
    public function updateTask(array $data, int $id): bool | string;

    // DELETE: method that deletes a task from the DataBase
    public function deleteTask(int $id): bool | string;
}

?>