<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/JWT.php';
use \Firebase\JWT\JWT;

class My_lib
{
  protected $_ci;
	var $pattern = "/.gif|.jpg|.jpeg|.png|.ico|.mp3|.mp4|.mpeg|.ogg|.pdf|.webm|.txt|.doc|.docb|.docx|.docm|.dot|.dotx|.dotm|.odt|.wbk|.GIF|.JPG|.JPEG|.PNG|.ICO|.MP3|.MP4|.MPEG|.OGG|.PDF|.WEBM|.xls|.XLS|.xlsx|.XLSX|.xlsm|.XLSM|.xltx|.XLTX|.xla|.XLA|.xlam|.XLAM|.xlw|.XLW|.csv|.CSV|.ods|.ODS|.jfif|.JFIF|.TXT|.DOC|.DOCB|.DOCX|.DOCM|.DOT|>DOTM|.DOTX|.WBK|.ppt|.PPT|.pps|.PPS|.pot|.POT|.pptx|.PPTX|.pptm|.PPTM|.ppsx|.PPSX|.sldx|.SLDX|.sldm|.SLDM|.pub|.PUB|.xps|.XPS|.zip|.ZIP|.rar|.RAR$/";
	var $allowedYypes = 'gif|jpg|jpeg|png|ico|mp3|mp4|mpeg|ogg|pdf|webm|txt|doc|docb|docx|docm|dot|dotx|dotm|wbk|GIF|JPG|JPEG|PNG|ICO|MP3|MP4|MPEG|OGG|PDF|WEBM|xls|XLS|xlsx|XLSX|csv|CSV|ods|ODS|jfif|JFIF|TXT|DOC|DOCB|DOCB|DOCX|DOCM|DOT|DOTM|DOTX|WBK|xlsm|XLSM|xltx|XLTX|xla|XLA|xlw|XLW|ppt|PPT|pps|PPS|pot|POT|pptx|PPTX|pptm|PPTM|ppsx|PPSX|sldx|SLDX|sldm|SLDM|pub|PUB|xps|XPS|zip|ZIP|rar|RAR';
	var $uploadPath  = '';

	function __construct()
	{
		$this->_ci =&get_instance();
		$this->_ci->load->helper('string');
	}

    function cekAuth()
	{
		$login = "no";
		if ($this->_ci->session->userdata('user_id') != '') {
			$login = "yes";
		}
		if ($login == "no") {
			redirect('login');
		}
	}

	function profile()
	{
		$url_api = base_url().'pengguna/api_pengguna?id='.$this->_ci->session->userdata('user_id');
        return curl( $url_api, null, null, $this->_ci->session->userdata('token'));
	}

    function set_upload_path($file_path){ //../assets/upload/images
		$this->uploadPath = $file_path;
	}

    function uploadFile($inputFileName, $i=NULL)
	{
		date_default_timezone_set('Asia/Jakarta');
		$this->_ci->load->library('upload');
		$random = random_string('alnum', 16);
		if ($i!==NULL) {
			$fileName = $_FILES[$inputFileName]['name'][$i];
		}
		else
		{
			$fileName = $_FILES[$inputFileName]['name'];
		}

		if (preg_match($this->pattern, $fileName, $matches,PREG_OFFSET_CAPTURE))
		{
			$ext = $matches[0][0];
			if ($i!==NULL) {
				$newname = $random."_".$i.$ext;
				$_FILES['userFile']['name'] = $_FILES[$inputFileName]['name'][$i];
                $_FILES['userFile']['type'] = $_FILES[$inputFileName]['type'][$i];
                $_FILES['userFile']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                $_FILES['userFile']['error'] = $_FILES[$inputFileName]['error'][$i];
				$_FILES['userFile']['size'] = $_FILES[$inputFileName]['size'][$i];
            } else {
				$newname = $random.$ext;
                $_FILES['userFile']['name'] = $_FILES[$inputFileName]['name'];
                $_FILES['userFile']['type'] = $_FILES[$inputFileName]['type'];
                $_FILES['userFile']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'];
                $_FILES['userFile']['error'] = $_FILES[$inputFileName]['error'];
                $_FILES['userFile']['size'] = $_FILES[$inputFileName]['size'];
			}

			$config = array(
				'file_name'     => $newname,
				'upload_path'   => $this->uploadPath,
				'allowed_types' => $this->allowedYypes,
				'overwrite'     => 1,
				'max_size'     => '99999999'
			);
			$this->_ci->upload->initialize($config);
			if ( ! $this->_ci->upload->do_upload('userFile'))
			{
				return array('status'=>false, 'data' => $this->_ci->upload->display_errors());
				//return "0";
			} else {
				// Continue processing the uploaded data
				$this->_ci->upload->data();
				return array(
					'status'=>true, 
					'data' => array(
						'file_name' => $newname,
						'type' => $_FILES['userFile']['type'],
						'ext' => $ext
						)
				);
			}
		} else {
			return array('status'=>false, 'data' => 'No match file type');
		}
	}

    public function hapusFile($nmFile)
	{
		$this->_ci->load->helper('file');
		// 'assets/upload/images'
		$dir = set_realpath($this->uploadPath);
		$string = file_exists($dir.$nmFile);
		if ($string == true){
			unlink($dir.$nmFile);
		}
	}

    public function kirimEmail($emailFrom=NULL, $nameSender=NULL, $emailTo=NULL, $nameRecipient=NULL, $subject=NULL, $message=NULL, $attachment=NULL)
	{
		$config['protocol']    = "mail"; // mail, smtp, sendmail
		$config['smtp_crypto'] = "ssl"; // tls, ssl
		$config['mailtype'] = "html"; // text or html
		$config['charset'] = 'utf-8'; // utf-8, iso-8859-1
		$config['newline'] = "\r\n";
		$config['crlf'] = "\r\n";
		$config['starttls'] = true;
		// $config['validation'] = FALSE; // bool whether to validate email or not
		$config['useragent'] = "CodeIgniter";
		$config['mailpath'] = "/usr/bin/sendmail";
		$config['wordwrap'] = TRUE;

		$config['smtp_host'] = "undppay.org"; // smtp.gmail.com, ssl://smtp.gmail.com,  smtp.office365.com
		$config['smtp_user'] = "_mainaccount@undppay.org"; // info@idsolutions.id, undp.id@undp.org
		$config['smtp_pass'] = "wqpFVc+^DyGd"; // Bismillah99, Lenovo22
		$config['smtp_port']    = "465"; // 465, 587, 25
		$config['smtp_timeout'] = "60";
		$config['priority'] = 1; // 1 = highest. 5 = lowest. 3 = normal.

		$this->_ci->load->library("email");
		$this->_ci->email->initialize($config);
		$this->_ci->email->set_newline("\r\n");

		$this->_ci->email->from($emailFrom, $nameSender);
		$this->_ci->email->to($emailTo, $nameRecipient);
		$this->_ci->email->subject($subject);
		$this->_ci->email->message($message);

		if (!is_null($attachment)) {
			$this->_ci->email->attach($attachment);
		}

		if($this->_ci->email->send()) {
			return array('status'=>true, 'data'=>$this->_ci->email->print_debugger());
		} else {
			return array('status'=>false, 'data'=>$this->_ci->email->print_debugger());
			// return show_error($this->_ci->email->print_debugger());
		}
	}

    public function admin_poto()
    {
        $this->_ci->load->model(array('common_model'));
        $poto = base_url().'assets/images/default_profile_img.png';
        $query = $this->_ci->common_model->Get_admin_kondisi(array('id'=>$this->_ci->session->userdata('user_id')));
        if (count($query) > 0) {
            if ($query[0]->thumbnail != "") {
                $poto = base_url().'assets/upload/images/'.$query[0]->thumbnail;
            }
        }
        return $poto;
	}
	
	public function validate_token($access_token)
	{
		try {
			$token = JWT::decode($access_token, $this->_ci->config->item('jwt_key'), array('HS256'));
			return $token;			
		} catch (Exception $e) {
			return $e;
		}
	}

	public function user_bidang($id)
	{
		$this->_ci->load->model(array(
            'pengguna/pengguna_model' => 'pengguna'
        ));
		$query = $this->_ci->pengguna->get(array('id'=>$id));
		if ($query->num_rows() <= 0) {
			return false;
		} else {
			$user = $query->row();
			if (!is_null($user->bidang_id)) {
				return $user->bidang_id;
			} else {
				return false;
			}
		}
	}
}
