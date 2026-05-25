<?php
class JobObserver implements SplSubject {
    private $data;
    private $observers=array();
    public function attach(SplObserver $observer){
        $this->observers[]=$observer;
    }

    public function detach(SplObserver $observer){
    // delete notify
    }

    public function notify(){
        foreach ($this->observers as $observer){
            $observer->update($this);
        }
    }

    public function getData(){
        return $this->data;
    }
    public function setData($data){
        $this->data = $data;
        $this->notify();
    }
}
class  BSISubscriiber implements SplObserver{

    public function update(SplSubject $subject){
        if($subject instanceof JobObserver){
            echo $subject->getData();
        }
    }
}

class  EnginnerSubscriiber implements SplObserver{

    public function update(SplSubject $subject){
        if($subject instanceof JobObserver){
            echo $subject->getData();
        }
    }
}
function Jobcreate(){
    return json_encode(['job_id'=>'000P','eng'=>'pppp']);
}

$jobsheetData=new JobObserver();
$jobsheetData->attach(new BSISubscriiber());
$jobsheetData->attach(new EnginnerSubscriiber());
$jobsheetData->setData(Jobcreate());
