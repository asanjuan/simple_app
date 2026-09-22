<?php

class BatchProgress {

    private $db;
    private $batchName;
    private $totalSteps;
    private $currentStep;
    private $errors;
	private $start;
	private $end;
	private $database_id;
	private $status;

    public function __construct($db, $batchName, $totalSteps) {
        $this->db = $db;
        $this->batchName = $batchName;
        $this->totalSteps = $totalSteps;
        $this->currentStep = 0;
        $this->errors = [];
		$this->status = "Pendiente";
		
		$this->insert();
    }

	public function start_process(){
		
		$this->status = "Iniciado";
		$this->save();
	}
	
	public function end_process(){
		$this->status = "Finalizado";
		$this->save();
		$this->update_end();
	}
	
    public function setTotalSteps($totalSteps) {
        $this->totalSteps = $totalSteps;
        $this->save();
    }

    public function getTotalSteps() {
        return $this->totalSteps;
    }

    public function getCurrentStep() {
        return $this->currentStep;
    }

    public function getCurrentStepPercent() {
        return (100 * $this->currentStep) / $this->totalSteps;
    }

    public function advanceStepCompleted() {
        $this->currentStep++;
        $this->save();
    }

    public function advanceStepFailed() {
        $this->currentStep++;
        $this->errors[] = "Error en el paso $this->currentStep";
        $this->save();
    }

    public function isCompleted() {
        return $this->currentStep >= $this->totalSteps;
    }

    public function getErrors() {
        return $this->errors;
    }

	

	private function save(){
		
        $stmt = $this->db->prepare("
            UPDATE batch_progress 
			SET batch_name = :batch_name, current_step = :current_step, total_steps = :total_steps, errors = :errors, status = :status
			where id = :id
        ");

        $stmt->bindValue(":batch_name", $this->batchName);
        $stmt->bindValue(":current_step", $this->currentStep);
        $stmt->bindValue(":total_steps", $this->totalSteps);
        $stmt->bindValue(":errors", count($this->errors));
		$stmt->bindValue(":status", $this->status);
		$stmt->bindValue(":id", $this->database_id);
        $stmt->execute();
	}
	
	private function update_end(){
		
        $stmt = $this->db->prepare("UPDATE batch_progress SET end = now() where id = :id ");
		$stmt->bindValue(":id", $this->database_id);
        $stmt->execute();
	}
    private function insert() {
		
        $stmt = $this->db->prepare("
            INSERT INTO batch_progress (batch_name, current_step, total_steps, errors, status, start)
            VALUES (:batch_name, :current_step, :total_steps, :errors, :status , now())
        ");

        $stmt->bindValue(":batch_name", $this->batchName);
        $stmt->bindValue(":current_step", $this->currentStep);
        $stmt->bindValue(":total_steps", $this->totalSteps);
        $stmt->bindValue(":errors", count($this->errors));
		$stmt->bindValue(":status", $this->status);
        $stmt->execute();
		$this->database_id = $this->db->lastInsertId();
    }
}
