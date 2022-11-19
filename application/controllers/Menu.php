<?php
defined('BASEPATH') or exit('No direct script access allwoed');

class Menu extends CI_Controller
{
    //untuk memblokir akses langsung dari url
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }
    public function index()
    {
        $data['title'] = 'menu management';
        $data['user'] = $this->db->get_where('user', ['email' => 
        $this->session->userdata('email')])->row_array();
         
        $data['menu'] = $this->db->get('user_menu')->result_array();

        $this->form_validation->set_rules('menu', 'Menu', 'required');

    if ($this->form_validation->run() ==false)  {
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('templates/footer');      
    } else {
   
        $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success" role="alert">
                New Menu Added!
            </div>'
            );    
        redirect('menu');
    }
}

public function submenu()
  {
      
    $data['title'] = 'Submenu management';
    $data['user'] = $this->db->get_where('user', ['email' => 
    $this->session->userdata('email')])->row_array();
    $this->load->model('Model_menu', 'menu');
    $data['Submenu'] = $this->menu->getSubmenu();
     
    $data['menu'] = $this->db->get('user_menu')->result_array();

    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('menu_id', 'Menu', 'required');
    $this->form_validation->set_rules('url', 'Url', 'required');
    $this->form_validation->set_rules('icon', 'Icon', 'required');

    
    if ($this->form_validation->run() ==false)  {
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('menu/submenu', $data);
        $this->load->view('templates/footer');      
    } else {
        $data = [
            'title' => $this->input->post('title'),
            'menu_id' => $this->input->post('menu_id'),
            'url' => $this->input->post('url'),
            'icon' => $this->input->post('icon'),
            'is_active' => $this->input->post('is_active'),
        ];
        $this->db->insert('user_sub_menu', $data);
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success" role="alert">
                New Sub Menu Added!
            </div>'
        );    
        redirect('menu/submenu');
  }
}

  public function submenuedit()
  {
      
    $this->form_validation->set_rules('title', 'title', 'required');
    $this->form_validation->set_rules('url', 'url', 'required');
    $this->form_validation->set_rules('icon', 'icon', 'required');

    
    if ($this->form_validation->run() == FALSE)  {
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success" role="alert">Change Failed</div>' 
        );
        redirect('menu/submenu');
    } else {
          $data = array(
              'title' => $_POST['title'],
              'menu_id' => $_POST['menu_id'],
              'url' => $_POST['url'],
              'icon' => $_POST['icon'],
              'is_active' => $_POST['is_active'],
        );
        $this->db->where('id', $_POST['id']);
        $this->db->update('user_sub_menu', $data);
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success" role="alert">Menu  Change</div>'
        );    
        redirect('menu/submenu');
  }
}
    public function editmenu()
    {
        
      $this->form_validation->set_rules('menu', 'Menu', 'required|trim');
      if ($this->form_validation->run() == FALSE)  {
          $this->session->set_flashdata(
              'message',
              '<div class="alert alert-danger" role="alert">Change Failed</div>' 
          );
          redirect('menu');
      } else {
          $this->db->set('menu',$this->input->post('menu'));
          $this->db->where('id',$this->input->post('id'));
          $this->db->update('user_menu', $data);
          $this->session->set_flashdata(
              'message',
              '<div class="alert alert-success" role="alert">Menu  Change</div>'
          );    
          redirect('menu');
      
      }
}
    public function hapus($id)
    {
      $this->db->where('id', $id);
      $this->db->delete('user_sub_menu');
      $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger" role="alert">Sub Menu Delete</div>'
        );    
        redirect('menu/submenu');
    }

    public function hapusmenu($id)
    {
      $this->db->where('id', $id);
      $this->db->delete('user_menu');
      $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger" role="alert">Menu Delete</div>'
        );    
        redirect('menu/index');
    }
}