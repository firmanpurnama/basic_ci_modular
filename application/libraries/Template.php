<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Template {
    protected $_ci;
    var $token;
    var $role = null;
    var $user_bidang = null;
    var $site_name = null;
    var $site_title = null;
    var $site_logo = null;
    function __construct()
    {
      $this->_ci =&get_instance();
      $this->_ci->load->library('my_lib');
    }
    
    function admin($view_page, $data=null)
    {
      $this->_ci->my_lib->cekAuth();
      $this->token = $this->_ci->my_lib->validate_token($this->_ci->session->userdata('token'));
      $this->user_bidang = $this->_ci->my_lib->user_bidang($this->token->id);
      $url_api = base_url().'settings/api_site';
      $site = curl( $url_api);
      if ($site->status_code == 200) {
        $this->site_name = $site->data->site_name;
        $this->site_title = $site->data->site_title;
      }
      $url_api_logo = base_url().'settings/api_logo';
      $logo = curl( $url_api_logo);
      if ($logo->status_code == 200) {
        $this->site_logo = $logo->data->image;
      }
      $data['my_profile'] = $this->_ci->my_lib->profile();
      $data['_side_menu']=$this->_ci->load->view('admin/side_menu', $data, true);
      $data['_header']=$this->_ci->load->view('admin/header', $data, true);
      $data['_content'] = $this->_ci->load->view($view_page, $data, true);
      $data['_footer'] = $this->_ci->load->view('admin/footer', $data, true);
      $this->_ci->load->view('admin/index', $data);
    }
  
    function login($view_page, $data=null)
    {
      $url_api = base_url().'settings/api_site';
      $site = curl( $url_api);
      if ($site->status_code == 200) {
        $this->site_name = $site->data->site_name;
        $this->site_title = $site->data->site_title;
      }
      $url_api_logo = base_url().'settings/api_logo';
      $logo = curl( $url_api_logo);
      if ($logo->status_code == 200) {
        $this->site_logo = $logo->data->image;
      }
      $data['_content'] = $this->_ci->load->view($view_page, $data, true);
      $this->_ci->load->view('signin/index', $data);
    }
  }