<?php

namespace App\Repository;
use App\Models\Course;
use App\Interfaces\CourseRepositoryInterface;
class CourseRepository implements CourseRepositoryInterface
{
    public function index(){
        return Course::all();
    }

    public function getById($id){
       return Course::findOrFail($id);
    }

    public function store(array $data){
       return Course::create($data);
    }

    public function update(array $data,$id){
       return Course::whereId($id)->update($data);
    }

    public function delete($id){
       Course::destroy($id);
    }
}
