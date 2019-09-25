<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Home_Controller {

	public function index()
	{
		$this->load->view('home/home');
	}
}
