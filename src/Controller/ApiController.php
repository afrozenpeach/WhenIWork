<?php
/**
 * Created by PhpStorm.
 * User: froze
 * Date: 11/14/2018
 * Time: 8:54 AM
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    private $shifts;

    public function listShifts()
    {
        $this->loadShifts();

        return new JsonResponse($this->shifts);
    }

    public function viewShift($shiftId)
    {
        $this->loadShifts();

        if (isset($this->shifts[$shiftId])) {
            return new JsonResponse($this->shifts[$shiftId]);
        }

        return new JsonResponse('Shift not found', 404);
    }

    public function createShift(Request $request)
    {
        $this->loadShifts();

        $id = 0;

        foreach ($this->shifts as $shift) {
            if ($shift->id > $id)
                $id = $shift->id;
        }

        $id++;

        $this->shifts[$id] = [
            'id' => $id,
            'employee' => $request->get('employee', 'John Doe'),
            'start' => $request->get('start', new \DateTime()),
            'end' => $request->get('end', new \DateTime('+1 Hour'))
        ];

        $this->saveShifts();

        return new JsonResponse('Success');
    }

    public function editShift(Request $request, $shiftId)
    {
        $this->loadShifts();

       if (isset($this->shifts[$shiftId])) {
           $employee = $request->get('employee');
           $start = $request->get('start');
           $end = $request->get('end');

           if ($employee) {
               $this->shifts[$shiftId]['employee'] = $employee;
           }

           if ($start) {
               $this->shifts[$shiftId]['start'] = new \DateTime($start);
           }

           if ($end) {
               $this->shifts[$shiftId]['end'] = new \DateTime($end);
           }

           $this->saveShifts();

           return new JsonResponse('Success');
       }

        return new JsonResponse('Shift not found', 404);
    }

    public function deleteShift($shiftId)
    {
        $this->loadShifts();

        unset($this->shifts[$shiftId]);

        $this->saveShifts();

        return new JsonResponse('Success');
    }

    private function loadShifts()
    {
        if (file_exists('shifts.json')) {
            $contents = file_get_contents('shifts.json');

            $this->shifts = json_decode($contents, true);
        } else {
            $this->shifts = [];
        }
    }

    private function saveShifts()
    {
        $contents = json_encode($this->shifts);

        file_put_contents('shifts.json', $contents);
    }
}
