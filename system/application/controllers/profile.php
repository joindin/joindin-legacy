<?php

class Profile extends Controller
{
	
	/**
	 * Redirects to profile display
	 */
	function index()
	{
		redirect('/user/profile/display', 'location', 302);
	}
	
	/**
	 * Displays the speaker profile
	 */
	function display()
	{
		$this->load->model('profile_model');
		$this->load->model('profile_im_account_model');
	    $this->load->model('profile_web_address_model');
	    $this->load->helper('address');
	    
	    $profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
	    
	    if($profile !== null) {
	    	// Get instant messaging accounts
	    	$imModels = $this->profile_im_account_model->findAll(array('profile_id' => $profile->getId()), 'id ASC');
	    	$viewVars['im_accounts'] = $imModels;
	    	
		    // Get social network accounts
		    $waModels = $this->profile_web_address_model->findAll(array('profile_id' => $profile->getId()), 'id ASC');
		    $viewVars['web_addresses'] = $waModels;
	    }
	    
	    $viewVars['profile'] = (null == $profile) ? null : $profile;
	    
	    $this->template->write_view('content','profile/display', $viewVars);
	    $this->template->render();
	}

	/**
	 * Lists access tokens for the speaker profile
	 */
	function access()
	{
		$this->load->model('profile_model');
		$this->load->model('profile_token_model');
		
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		if(null === $profile) {
			redirect('user/profile', 'location', 400);
		}
		
		$viewVars = array();
		
		$allTokens = $this->profile_token_model->findAll(
			array('profile_id' => $profile->getId()),
			'created DESC'
		);
		
		$tokenData = array();
		foreach($allTokens as $token) {
			$tokenData[] = $token->getData();
		}
		
		$viewVars['tokens'] = $tokenData;
		
		$this->template->write_view('content','profile/token_list', $viewVars);
	    $this->template->render();
	}
	
	/**
	 * Displays the token add/edit form
	 * @param $id
	 */
	function token($id = null)
	{
		$this->load->model('profile_model');
		$this->load->model('profile_token_model');
		
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		
		if(null === $profile) {
			redirect('user/profile', 'location', 400);
		}
		
		$viewVars = array();
		
		if(!empty($_POST)) {
			$token = new Profile_token_model($_POST);
			if($token->save()) {
				$this->session->set_flashdata('msg', 'Token saved successfully!');
			    redirect('user/profile/access', 'location', 302);
			}
			else {
				$viewVars['msg_error'] = $token->getErrors();
			}
		}
		else if($id === null) {
			$token = new Profile_token_model(array(
				'profile_id' => $profile->getId(),
				'access_token' => $this->profile_token_model->generate(),
				'created' => mktime(),
			));
		}
		else {
			$token = $this->profile_token_model->find($id);
		}

		$viewVars['token'] = (null !== $token) ? $token->getData() : null;
		
		$this->template->write_view('content','profile/token_form', $viewVars);
	    $this->template->render();
	}
	
	/**
	 * Deletes a token from the system
	 * @param $id
	 */
	function token_delete($id)
	{
		if(null === $id) {
			redirect('user/profile/access', 'location', 400);
		}
		$this->load->model('profile_model');
		$this->load->model('profile_token_model');

		// Check if the user has a profile 
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		
		if(null === $profile) {
			redirect('user/profile/display', 'location', 400);
		}
		
		// Try to delete the account
		$token = $this->profile_token_model->find($id);
		if(null !== $token && $profile->getId() == $token->getProfileId()) {
			$token->delete();
			$this->session->set_flashdata('msg', 'Token deleted successfully!');
		} else {
			$this->session->set_flashdata('msg_error', 'An error occurred while deleting the token, try again.');
		}
		
		redirect('user/profile/access', 'location', 302);
	}
	
	/**
	 * Edit a speaker profile
	 */
	function edit()
	{
        $this->load->helper('form');
	    $this->load->model('profile_model');
	    $this->load->model('country_model');
	    
	    $viewVars = array();
	    
	    $profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
	    if(null === $profile) {
	    	$profile = new Profile_model(array('user_id' => $this->session->userdata('ID')));
	    }

	    if(!empty($_POST)) {
	    	
	    	// Check if we need to delete the picture
	    	if(isset($_POST['delete_picture']) && $_POST['delete_picture'] == 1) {
	    		$profile->deletePicture();
	    	}
	    	
	    	// Validate the model
	    	$validData = $profile->validate($_POST);
	    	$uploaded = true;
	    		    	
	    	// Check if we need to upload a picture
	    	if($validData && isset($_FILES['picture']['tmp_name']) && !empty($_FILES['picture']['tmp_name'])) {
	    		// Configure the upload class
	    		$uploadConfig = array (
		    		'upload_path' => BASEPATH . '../inc/img/speaker_pictures/',
		    		'allowed_types' => 'gif|jpg|png',
		    		'max_size' => '250',
		    		'max_width' => '150',
		    		'max_height' => '150',
	    			'overwrite' => true,
		    	);
		    	$this->load->library('upload', $uploadConfig);
		    	
		    	// Replace the filename with the name of the speaker
		    	$fileName = strtolower(preg_replace('/[^A-Za-z0-9_\.]/', '', $profile->getFullName()));
		    	preg_match('/.*\.(gif|jpg|png)$/i', $_FILES['picture']['name'], $matches);
		    	if(count($matches) == 2) {
		    		$fileExtension = $matches[1];
	    			$_FILES['picture']['name'] = $fileName . '.' . $fileExtension;
		    	}
		    	
		    	// Try to upload the picture
		    	$uploaded = $this->upload->do_upload('picture');
		    	if($uploaded) {
		    		// Update the profile with the new picture
		    		$uploadData = $this->upload->data();
		    		// Fetch the old picture
		    		$oldPicture = $profile->getPicture();
		    		// Set the new picture
		    		$profile->setPicture('/inc/img/speaker_pictures/' . $uploadData['file_name']);
		    		// Remove the old picture
		    		if(!empty($oldPicture) && ($oldPicture != $profile->getPicture()) && file_exists(BASEPATH . '..' . $oldPicture)) {
		    			unlink(BASEPATH . '..' . $oldPicture);
		    		}
		    	}
	    	}

	    	// Check if everything went ok
	        if($validData && $uploaded) {
	        	// Save the profile
	        	$profile->save();
	        	// Redirect and display a message
	            $this->session->set_flashdata('msg', 'Profile updated successfully!');
			    redirect('user/profile', 'location', 302);
	        } else {
	            $viewVars['msg_error'] = $profile->getErrors();
	            if(isset($this->upload)) {
	            	$viewVars['msg_error'] = array_merge($viewVars['msg_error'], $this->upload->error_msg);
	            }
	        }
	    }
	    
	    $viewVars['profile'] = $profile->getData();
	    $viewVars['countries'] = $this->country_model->getList();
	    
        $this->template->write_view('content','profile/form', $viewVars);
	    // Render the template
		$this->template->render();
	}
	
	
	/**
	 * Deletes the speaker profile
	 */
	function delete()
	{
		$this->load->model('profile_model');
	    $this->load->model('profile_im_account_model');
	    $this->load->model('profile_sn_account_model');
	    
	    $profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
	    if(null !== $profile) {
	    	
	    	// Delete all the connected instant messaging accounts
		    $imAccounts = $this->profile_im_account_model->findAll(array('profile_id' => $profile->getId()));
		    foreach($imAccounts as $account) {
	    		$account->delete();
	    	}
	    	
	    	// Delete all the connected social network accounts
		    $snAccounts = $this->profile_sn_account_model->findAll(array('profile_id' => $profile->getId()));
		    foreach($snAccounts as $account) {
	    		$account->delete();
	    	}
	   		 
	    	// Delete the profile
	    	$profile->delete();
	    }
	    
	    // redirect back to the profile page
	    redirect('user/profile/display', 'location', 302);
	}
	
	
	/**
	 * Shows the web address add/edit form
	 * @param $id
	 */
	function web($id = null)
	{
		$this->load->model('profile_model');
		$this->load->model('profile_web_address_model');
		$this->load->model('profile_web_address_type_model');
		
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		
		if(null === $profile) {
			redirect('user/profile/display', 'location', 400);
		}
		
		$viewVars = array();
		
		if(!empty($_POST)) {
			$account = new Profile_web_address_model($_POST);
			
			// Try to save the model
			if($account->save()) {
				// Redirect and display a message
	            $this->session->set_flashdata('msg', 'Changes saved successfully!');
			    redirect('user/profile', 'location', 302);
			} else {
				$viewVars['account'] = $account->getData();
				$viewVars['msg_error'] = $account->getErrors();
			}
		} else if(null === $id) {
			$account = new Profile_web_address_model(array('profile_id' => $profile->getId()));
			$viewVars['account'] = $account->getData();
		} else {
			$account = $this->profile_web_address_model->find($id);
			if(!is_null($account) && $profile->getId() == $account->getProfileId()) {
				$viewVars['account'] = $account->getData();
			} else {
				$viewVars['account'] = null;
			}
		}
		
		$viewVars['types'] = $this->profile_web_address_type_model->getList();
		
		$this->template->write_view('content','profile/web_form', $viewVars);
	    $this->template->render();
	}
	
	
	/**
	 * Deletes a social network account for the user
	 * @param $id
	 */
	function web_delete($id = null) 
	{
		if(null === $id) {
			redirect('user/profile/display', 'location', 400);
		}
		$this->load->model('profile_model');
		$this->load->model('profile_web_address_model');

		// Check if the user has a profile 
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		
		if(null === $profile) {
			redirect('user/profile/display', 'location', 400);
		}
		
		// Try to delete the account
		$account = $this->profile_web_address_model->find($id);
		if(null !== $account && $profile->getId() == $account->getProfileId()) {
			$account->delete();
			$this->session->set_flashdata('msg', 'Web address deleted successfully!');
		} else {
			$this->session->set_flashdata('msg_error', 'An error occurred while deleting the web address, please try again.');
		}
		
		redirect('user/profile/display', 'location', 302);
	}
	
	
	/**
	 * Shows the add/edit form for instant messaging accounts
	 * @param $id
	 */
	function im($id = null) 
	{
		$this->load->model('profile_model');
		$this->load->model('profile_im_account_model');
		$this->load->model('profile_im_account_network_model');
		
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		
		if(null === $profile) {
			redirect('user/profile/display', 'location', 400);
		}
		
		$viewVars = array();
		
		if(!empty($_POST)) {
			$account = new Profile_im_account_model($_POST);
			
			// Try to save the model
			if($account->save()) {
				// Redirect and display a message
	            $this->session->set_flashdata('msg', 'Instant messaging account saved successfully!');
			    redirect('user/profile', 'location', 302);
			} else {
				$viewVars['account'] = $account->getData();
				$viewVars['msg_error'] = $account->getErrors();
			}
		} 
		else if($id === null) {
			$account = new Profile_im_account_model(array('profile_id' => $profile->getId()));
			$viewVars['account'] = $account->getData();
		}
		else {
			$account = $this->profile_im_account_model->find($id);
			if(!is_null($account) && $profile->getId() == $account->getProfileId()) {
				$viewVars['account'] = $account->getData();
			} else {
				$viewVars['account'] = null;
			}
		}
		
		$viewVars['networks'] = $this->profile_im_account_network_model->getList();
		
		$this->template->write_view('content','profile/im_form', $viewVars);
	    $this->template->render();
	}
	
	
	/**
	 * Deletes an instant messaging account from the users profile.
	 * @param $id
	 * @return unknown_type
	 */
	function im_delete($id = null)
	{
		if(null === $id) {
			redirect('user/profile/display', 'location', 400);
		}
		$this->load->model('profile_model');
		$this->load->model('profile_im_account_model');

		// Check if the user has a profile 
		$profile = $this->profile_model->findByUserId($this->session->userdata('ID'));
		
		if(null === $profile) {
			redirect('user/profile/display', 'location', 400);
		}
		
		// Try to delete the account
		$account = $this->profile_im_account_model->find($id);
		if(null !== $account && $profile->getId() == $account->getProfileId()) {
			$account->delete();
			$this->session->set_flashdata('msg', 'Instant messaging account deleted successfully!');
		} else {
			$this->session->set_flashdata('msg_error', 'An error occurred while deleting the account, please try again.');
		}
		
		redirect('user/profile/display', 'location', 302);
	}
	
}