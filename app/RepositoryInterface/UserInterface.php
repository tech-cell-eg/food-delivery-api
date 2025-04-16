<?php
namespace App\RepositoryInterface;
interface UserInterface
{
    public function register(array $data);
    public function login(array $data);
    public function logout();
    public function getUserById($id);
    public function updateUser($id, array $data);
    public function deleteUser($id);
    public function getAllUsers();
    public function getUserByEmail($email);
    public function updateUserPassword($id, $password);
    public function updateUserEmail($id, $email);
    public function checkEmailExists($email);
}