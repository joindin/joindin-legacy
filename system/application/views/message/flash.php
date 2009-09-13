<?php
/**
 * Reads data from the flashmessenger.
 */
 
// Check for info messages
if($this->session->flashdata('message') !== false) {
    $this->load->view('message/info', array('message' => $this->session->flashdata('message')));
}

// Check for error messages
if($this->session->flashdata('error') !== false) {
    $this->load->view('message/error', array('message' => $this->session->flashdata('error')));
}
