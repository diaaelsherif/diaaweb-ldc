<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Interfaces\CourseRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\DB;
class CourseController extends Controller
{

    private CourseRepositoryInterface $courseRepositoryInterface;

    public function __construct(CourseRepositoryInterface $courseRepositoryInterface)
    {
        $this->courseRepositoryInterface = $courseRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->courseRepositoryInterface->index();

        return ApiResponseClass::sendResponse(CourseResource::collection($data),'',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $details =[
            'name' => $request->name,
            'description' => $request->description
        ];
        DB::beginTransaction();
        try{
             $course = $this->courseRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new CourseResource($course),'Course Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $course = $this->courseRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new CourseResource($course),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        $updateDetails =[
            'name' => $request->name,
            'description' => $request->description
        ];
        DB::beginTransaction();
        try{
             $course = $this->courseRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Course Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->courseRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Course Delete Successful','',204);
    }
}
