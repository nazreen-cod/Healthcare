<?php

namespace App\Http\Controllers\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class Contactcontroller extends Controller
{
    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->tablenamee = 'admin';
    }


    //show database admin
    public function addadmin(){
        $admin =  $this->database->getReference($this->tablenamee)->getValue();
        return view('firebase.contact.addadmin',compact('admin'));
    }

    //register admin
    public function createadmin(){
        return view('firebase.contact.createadmin');
    }

    //store admin
    public function storeadmin(request $request){


        $postData = [
            'fname' => $request ->fullname,
            'email' => $request ->email,
            'phone' => $request ->phone,
            'password' =>  \Illuminate\Support\Facades\Hash::make($request->password),
        ];
        $postRef =$this->database->getReference($this->tablenamee)->push($postData);
        if($postRef){
            return redirect('addadmin')->with('success','Admin Added Successfully');
        }
        else{
            return redirect('createadmin')->with('error','Admin Not Added');
        }
    }

    //edit admin
    public function editadmin($id){
        $key = $id;
        $editdata = $this->database->getReference($this->tablenamee)->getChild($key)->getValue();
        if($editdata){
            return view('firebase.contact.editadmin',compact('editdata','key'));
        }
        else{
            return redirect('addadmin')->with('status','Contact ID not Found');
        }

    }

    //edit admin
    public function updateadmin(Request $request, $id)
    {
        // Fetch the existing admin data from the database
        $existingData = $this->database->getReference($this->tablenamee.'/'.$id)->getValue();

        // Prepare the data to be updated
        $updateData = [
            'fname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Only hash the password if it has been changed
        if ($request->password && $request->password !== $existingData['password']) {
            $updateData['password'] = bcrypt($request->password); // Hashing the new password
        } else {
            // Keep the existing password if not changed
            $updateData['password'] = $existingData['password'];
        }

        // Update the data in the database
        $res_updated = $this->database->getReference($this->tablenamee.'/'.$id)->update($updateData); // this is the root reference

        if ($res_updated) {
            return redirect('addadmin')->with('success', 'Admin Updated Successfully');
        } else {
            return redirect('addadmin')->with('error', 'Admin Not Updated');
        }
    }


    //delete admin
    public function deleteadmin($id){
        $key = $id;
       $del_data = $this->database->getReference($this->tablenamee.'/'.$key)->remove();
       if($del_data){
        return redirect('addadmin')->with('success','Admin Deleted Successfully');
       }
       else{
        return redirect('addadmin')->with('error','Admin Not Deleted');
       }

    }


}
