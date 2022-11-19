<?php

class admin extends CI_Controller
{
    //untuk memblokir akses langsung dari url
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }
    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer'); 
    }
    public function role()
    {
       $data['title'] = 'Role';
       $data['user'] = $this->db->get_where('user', 
       ['email' => $this->session->userdata('email')])->row_array();

       $data['role'] = $this->db->get('user_role')->result_array();

       if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/role', $data);
            $this->load->view('templates/footer'); 
       } else {
            $this->db->insert('user_role', ['role' => $this->input->post('role')]);
            $this->session->set_flashdata(
                'message', '<div class="alert alert-success" role="alert"> 
            New Role Added!
        </div>'
            );
            redirect('admin/role');
       }
    }
    public function editrole()
    {
        $this->from_validation->set_rules('role', 'Role', 'required|trim');
        if ($this->from_validation->run() == false) {
            $this->session->set_flashdata(
                'message', 
                '<div class="alert alert-danger" role="alert">Change Failed</div>'
            );
            redirect('admin/role');
        } else {
            $this->db->set('role', $this->input->post('role'));
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('user_role');
            $this->session->set_flashdata(
                'message', 
                '<div class="alert alert-success" role="alert">Role Change</div>'
            );
            redirect('admin/role');
        }
    }
    public function hapusrole($id)
    {

            $this->db->where('id', $id);
            $this->db->delete('user_role');
            $this->session->set_flashdata(
                'message', 
                '<div class="alert alert-success" role="alert">Role Deleted</div>'
            );
            redirect('admin/role');
        }
    
    public function roleAccess($role_id)
    {
        $data['title'] = 'Role Access';
        $data['user'] = $this->db->get_where('user', 
        ['email' => $this->session->userdata('email')])->row_array();
 
        $data['role'] = $this->db->get_where('user_role', 
        ['id' => $role_id])->row_array();
        $this->db->where('id !=', 1); 
        $data['menu'] = $this->db->get('user_menu')->result_array(); 


        $this->form_validation->set_rules('role', 'Role', 'required');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/role-access', $data);
            $this->load->view('templates/footer'); 
        } else {
            $this->db->insert('user_role', ['role' => $this->input->post('role')]);
            $this->session->set_flashdata(
                'message', '<div class="alert alert-success" role="alert"> 
            New Role Added!
        </div>'
            );
            redirect('admin/role');
       }
    }
    public function changeAccess()
    {
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId');

        $data = [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ];
        $result = $this->db->get_where('user_access_menu', $data);
        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $data);
        } else {
            $this->db->delete('user_access_menu', $data);
        }

        $this->session->set_flashdata(
            'message', 
            '<div class="alert alert-success" role="alert">Access Change</div>'
        );
    }
}