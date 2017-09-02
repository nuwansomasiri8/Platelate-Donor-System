<?php
class Users extends CI_Controller
{
    public function register()
    {
        $data['title'] = 'Registration';
        $this->form_validation->set_rules('employee_firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('employee_lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('employee_gender', 'Gender', 'required');
        $this->form_validation->set_rules('employee_teleNo', 'Telephone', 'required|max_length[10]|min_length[10]');
        $this->form_validation->set_rules('employee_NIC', 'NIC', 'trim|required|min_length[10]|max_length[20]');
        $this->form_validation->set_rules('employee_occupation', 'Occupation', 'required');
        $this->form_validation->set_rules('employee_email', 'Email', 'required|valid_email|callback_check_email_exists');
        $this->form_validation->set_rules('employee_username', 'Username', 'trim|required|callback_check_username_exists');
        $this->form_validation->set_rules('employee_password','Password','required|callback_valid_password');
        $this->form_validation->set_rules('employee_password2', 'Confirm Password', 'matches[employee_password]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header');
            $this->load->view('users/register', $data);
            $this->load->view('template/footer');
        } else {
            //die('Continue');
            //Encrypte password
            $enc_password = md5($this->input->post('employee_password'));
            $this->user_model->register($enc_password);

            //set message
            $this->session->set_flashdata('user_registered', 'User are now registered and can log in');

            redirect('users/register');
        }
    }
    // Check if username exists
    public function check_username_exists($employee_username)
    {
        $this->form_validation->set_message('check_username_exists', 'That username is taken. Please choose a different one');
        if ($this->user_model->check_username_exists($employee_username)) {
            return true;
        } else {
            return false;
        }
    }

    //check if email exists
    public function check_email_exists($employee_email)
    {
        $this->form_validation->set_message('check_email_exists', 'That email  is taken.Please choose a different one');
        if ($this->user_model->check_email_exists($employee_email)) {
            return true;
        } else {
            return false;
        }
    }

    //login

    public function login()
    {
        $data['title'] = 'Sign In';
        $this->form_validation->set_rules('employee_username', 'Username', 'required');
        $this->form_validation->set_rules('employee_password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('template/header');
            $this->load->view('users/login', $data);
            $this->load->view('template/footer');
        } else {
            //get username
            $employee_username = $this->input->post('employee_username');
            //get password
            $password = md5($this->input->post('employee_password'));
            //loggin user
            $employee_id = $this->user_model->login($employee_username, $password);
            if ($employee_id) {
                //create the session
                //die('SUCCESS');
                $user_data = array(
                    'employee__id'=>$employee_id,
                    'employee_username'=>$employee_username,
                    'logged_in'=>true
                );

                $this->session->set_userdata($user_data);


                //set message
                $this->session->set_flashdata('user_loggedin', 'You are now logged in');



                redirect('users/register');
            } else {
                //set message
                $this->session->set_flashdata('login_failed', 'You are logged into fail');

                redirect('users/login');
            }
        }
    }
    //log user out

    public function logout(){
        //unset user data
        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('employee_id');
        $this->session->unset_userdata('employee_username');

        //set message
        $this->session->set_flashdata('user_loggedout','You are now logged out');
        redirect('users/login');
    }

    //    valid password
    public function valid_password($employee_password)
    {
        $employee_password = trim($employee_password);
        $regex_lowercase = '/[a-z]/';
        $regex_uppercase = '/[A-Z]/';
        $regex_number = '/[0-9]/';
        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';
        if (empty($employee_password))
        {
            $this->form_validation->set_message('valid_password', 'The {field} field is required.');
            return FALSE;
        }
        if (preg_match_all($regex_lowercase, $employee_password) < 1)
        {
            $this->form_validation->set_message('valid_password', 'The {field} field must be at least one lowercase letter.');
            return FALSE;
        }
        if (preg_match_all($regex_uppercase, $employee_password) < 1)
        {
            $this->form_validation->set_message('valid_password', 'The {field} field must be at least one uppercase letter.');
            return FALSE;
        }
        if (preg_match_all($regex_number, $employee_password) < 1)
        {
            $this->form_validation->set_message('valid_password', 'The {field} field must have at least one number.');
            return FALSE;
        }
        if (preg_match_all($regex_special, $employee_password) < 1)
        {
            $this->form_validation->set_message('valid_password', 'The {field} field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));
            return FALSE;
        }
        if (strlen($employee_password) < 5)
        {
            $this->form_validation->set_message('valid_password', 'The {field} field must be at least 5 characters in length.');
            return FALSE;
        }
        if (strlen($employee_password) > 32)
        {
            $this->form_validation->set_message('valid_password', 'The {field} field cannot exceed 32 characters in length.');
            return FALSE;
        }
        return TRUE;
    }







/*//    valid password
    public function valid_password($employee_password){
        $this->form_validation->set_message('valid_password', 'this is invalid password');
        if ($this->user_model->valid_password($employee_password)) {
            return true;
        } else {
            return false;
        }
    }*/
}
?>