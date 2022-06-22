<?php
class Headers {
	
	private $title;
	private $description;
	private $canonical = '';
	private $h1;
	private $error_title;
	private $error_description;
	 
	public function setTitle($language) {
		$this->title = $language;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setDescription($language) {
		$this->description = $language;
	}
	
	public function getDescription() {
		return $this->description;
	}

	public function setCanonical($language) {
		$this->canonical = $language;
	}
	
	public function getCanonical() {
		return $this->canonical;
	}
	
	public function setH1($language) {
		$this->h1 = $language;
	}
	
	public function getH1() {
		return $this->h1;
	}
	
	public function setErrorTitle($language) {
		$this->error_title = $language;
	}
	
	public function getErrorTitle() {
		return $this->error_title;
	}
	
	public function setErrorDescription($language) {
		$this->error_description = $language;
	}
	
	public function getErrorDescription() {
		return $this->error_description;
	}
	
  
} 