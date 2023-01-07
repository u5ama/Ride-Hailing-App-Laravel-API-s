<?php
namespace App\FireBase;
use Kreait\Laravel\Firebase\Facades\FirebaseDatabase;

class FireBase
{

    public static function store($id,$data){
       $node = FirebaseDatabase::getReference('tracking/'.$id)->set($data);
        $val = $node->getSnapshot()->getValue();
        return $val;
    }public static function updateNode($id,$node,$data){
       $node = FirebaseDatabase::getReference('tracking/'.$id.'/'.$node)->set($data);
        $val = $node->getSnapshot()->getValue();
        return $val;
    }
    public static function show($id){
       $node = FirebaseDatabase::getReference('tracking/'.$id);
        $val = $node->getSnapshot()->getValue();
        return $val;
    } public static function showTracking(){
       $node = FirebaseDatabase::getReference('tracking');
        $val = $node->getSnapshot()->getValue();
        return $val;
    }
    public static function delete($id){
       $node = FirebaseDatabase::getReference('tracking/'.$id);
        $val = $node->remove();
        return $val;
    }
    public static function storeuser($id,$data){
        $node = FirebaseDatabase::getReference('passenger/'.$id)->set($data);
        $val = $node->getSnapshot()->getValue();
        return $val;
    }
    public static function storedriver($id,$data){
    $node = FirebaseDatabase::getReference('driver/'.$id)->set($data);
    $val = $node->getSnapshot()->getValue();
    return $val;
   }
    public static function deleteuser($id){
        $node = FirebaseDatabase::getReference('passenger/'.$id);
        $val = $node->remove();
        return $val;
    }
    public static function deletedriver($id){
        $node = FirebaseDatabase::getReference('driver/'.$id);
        $val = $node->remove();
        return $val;
    }
    public static function updateDriver($id,$node,$data){
        $node = FirebaseDatabase::getReference('driver/'.$id.'/'.$node)->set($data);
        $val = $node->getSnapshot()->getValue();
        return $val;
    }



}
