<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        if ($this->form_validation->run() == false) {

            $data['title'] = 'login pages';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            $this->_login();
        }
    }
    

    public function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        if ($user) {
            if ($user['is_active'] == 1) {
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    } else {
                    redirect('user');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"> wrong password!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"> this email has been activated!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"> this email is not registered!</div>');
            redirect('auth');
        }
    }

    public function registration()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[5]|matches[password2]', [
            'matches' => 'password dont match',
            'min_length' => 'password to shoort',
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');
        if ($this->form_validation->run() == false) {

            $data['title'] = 'registration pages';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email', true);
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($email),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];
            $this->db->insert('user', $data);
            $this->db->insert('user_token', $user_token);
            $this->_sendEmail($token, 'verify');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert"> 
            congralation! your account has been created, Please Activate your Account
            </div>');
            redirect('auth');
        }
    }
    //function untuk mengirim verifikasi ke mail
    private function _sendEmail($token, $type)
    {
        $this->load->library('email');
        $config = array();
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_user'] = 'unaacancii@gmail.com';//isi dengan email
        $config['smtp_pass'] = 'keqitlctetiqsnfo';
        $config['smtp_port'] = '465';
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");

        $this->email->from('unaacancii@gmail.com', 'Web Programing SMKN8Jember');
        $this->email->to($this->input->post('email'));

        if ($type == 'verify') {
            $this->email->subject('Account Verivication');
            $this->email->message('Clik this link to reset you password : 
            <a href="'. base_url() . 'auth/resetpassword?email=' .
             $this->input->post('email') . 'gtoken=' . urlencode($token).
            '">Reset Password</a>);
            ');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Clik this link to reset you account : 
            <a href="'. base_url() . 'auth/verify?email=' . 
            $this->input->post('email') . 'gtoken=' . urlencode($token).
            '">Activate</a>);
            ');
        }
        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }
    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this ->input->get('token');


            $user = $this->db->get_where('user', ['email' => $email])->row_array();
            if ($user) {
                $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

                if ($user_token) {
                    if (time() - $user_token['data_created'] < ( 60 * 60 * 24 )) {
                        $this->set->set('is_active', 1);
                        $this->set->where('email', $email );
                        $this->set->update('user');
                        $this->set->delete('user_token', ['email' =>$email]);
                        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' .
                        $email . ' has been Activate!Please login</div>');
                        redirect('auth');
                    } else {
                        $this->set->delete('user', ['email' =>$email]);
                        $this->set->delete('user_token', ['email' =>$email]);
                        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                        Account activate failed!token expired.</div>');
                        redirect('auth');
                    }
                } else {
                     $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                        Account activate failed!wrong email.</div>');
                        redirect('auth');
                }
            }
        }
        
    public function logout()
    {

        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');
        $this->session->set_flashdata('message', '<div class="alert-success" role="alert"> You have been logged Out! </div>');
        redirect('auth');
    }
    
    public function blocked()
    {
        $this->load->view('auth/blocked');
    }

    public function resetpassword()
    {
        $email = $this->input->get('email');
        $token = $this ->input->get('token');
        $user = $this->db->get_where('user', ['email' =>$email])->row_array();
        if ($user){
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
                $this->session->set_userdata('reset_email', $email);
                $this->changePassword();
            } else {
                $this->session->set_flashdata('message', '<div class="alert-danger" role="alert"> 
                Reset password failed!Wrong token </div>');
                redirect('auth/forgotpassword');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert-danger" role="alert"> 
            Reset password failed!Wrong email </div>');
            redirect('auth/forgotpassword');
        }
    }
    public function changepassword()
    {
        if($this->session->userdata('reset+email')) {
            redirect('auth');
        }
        $this->form_validation->set_rules('password1', 'Password1', 'trim|required|min');
        $this->form_validation->set_rules('password2', 'Repet Pasword', 'trim|required|min_lenght[3]matches[password]');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/Change_password');
            $this->load->view('templates/auth_footer');
        } else {
            $password =  hash($this->input->post('password'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');


            $this->dbt->set('password', $password);
            $this->db->where('email', $email );
            $this->db->update('user');

            $this->session->unset_userdata('reset_email');
            $this->session->set_flashdata('message', '<div class="alert-success" role="alert"> 
            Password has been change!Please </div>');
            redirect('auth');
        }
    }
    public function forgotPassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot_password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user = $this->db->get_where('user', ['email' =>$email, 'is_active' =>1])->row_array();

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('user_token', $user_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('message', '<div class="alert-success" role="alert"> 
                Please check your email to reset password!</div>');
                redirect('auth/forgotpassword');
            } else {
                $this->session->set_flashdata('message', '<div class="alert-danger" role="alert"> 
                Email is not registered or activated!</div>');
                redirect('auth/forgotpassword');
        }
    }
}}