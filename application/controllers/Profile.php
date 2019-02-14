<?php
defined("BASEPATH")or exit("ERROR");

/**
 *  Author: Virgorasion
 */
class Profile extends CI_controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->model("ProfileModel");
        $this->load->library("datatables");
    }

    public function index()
    {
        if (@$_SESSION['username'] != null) {
            if ($_SESSION['hakAkses'] == 1) {
                $query = $this->ProfileModel->dataAdmin(@$_SESSION['kode_admin']);
                $data['data'] = $query;
                $this->load->view('v_profile',$data);
            }elseif ($_SESSION['hakAkses'] == 2) {
                $query = $this->ProfileModel->dataInstansi(@$_SESSION['kode_instansi']);
                $data['data'] = $query;
                $this->load->view('v_profile',$data);
            }elseif ($_SESSION['hakAkses'] == 3) {
                $query = $this->ProfileModel->dataSiswa(@$_SESSION['id_siswa']);
                $data['data'] = $query;
                $this->load->view('v_profile',$data);
            }
        }else {
            $data['csrf'] = array(
                'token' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            $this->load->view('LoginMember', $data);
        }
    }

    public function DataInstansiAPI($kodeInstansi = NULL)
    {
        header("Content-Type: application/json");
        echo $this->ProfileModel->getDataInstansi($kodeInstansi);
    }

    public function DataSiswaAPI($kodeInstansi)
    {
        header("Content-Type: application/json");
        echo $this->ProfileModel->getDataSiswa($kodeInstansi);
    }

    public function DataRegistrationAPI()
    {
        header("Content-Type: application/json");
        echo $this->ProfileModel->getDataRegistration();
    }

    public function UbahData()
    {
        $p = $this->input->post();
        $query = "";
        if ($p['ubahPassword'] == "") {
            if ($_SESSION['hakAkses'] == 1) {
                $data = array(
                    'username' => $p['ubahUsername']
                );
                $query = $this->db->update("tb_admin",$data,$_SESSION['id_admin']);
            }elseif ($_SESSION['hakAkses'] == 2) {
                $data = array(
                    'username' => $p['ubahUsername']
                );
                $query = $this->db->update("tb_instansi",$data,$_SESSION['kode_instansi']);
            }elseif ($_SESSION['hakAkses'] == 3) {
                $data = array(
                    'username' => $p['ubahUsername']
                );
                $query = $this->db->update("tb_siswa",$data,$_SESSION['id_siswa']);
            }
        }else {
            if ($_SESSION['hakAkses'] == 1) {
                $data = array(
                    'username' => $p['ubahUsername'],
                    'password' => md5($p['ubahPassword'])
                );
                $query = $this->db->update("tb_admin",$data,array('kode_admin' => $_SESSION['kode_admin']));
            }elseif ($_SESSION['hakAkses'] == 2) {
                $data = array(
                    'username' => $p['ubahUsername'],
                    'password' => md5($p['ubahPassword'])
                );
                $query = $this->db->update("tb_instansi",$data,array('kode_instansi' => $_SESSION['kode_instansi']));
            }elseif ($_SESSION['hakAkses'] == 3) {
                $data = array(
                    'username' => $p['ubahUsername'],
                    'password' => md5($p['ubahPassword'])
                );
                $query = $this->db->update("tb_siswa",$data,array('id_siswa' => $_SESSION['id_siswa']));
            }
        }
        if ($query) {
            $this->session->set_tempdata('succ', 'Berhasil Ubah Data Profile',5);
            redirect(site_url("Profile"));
        }else {
            $this->session->set_tempdata('fail', 'Gagal Ubah Data Profile, hubungi admin !',5);
            redirect(site_url("Profile"));
        }
    }

    public function NamaInstansiAPI()
    {
        header("Content-Type: application/json");
        $query = $this->ProfileModel->getNamaInstansi();
        echo json_encode($query);
    }

    public function TambahSiswa()
    {
        $post = $this->input->post();
        $data = array(
            'kode_instansi' => $post['addInstansi'],
            'kode_program' => $post['addProgram'],
            'nama' => $post['addNama'],
            'hak_akses' => 3,
            'nis' => $post['addNis'],
            'nisn' => $post['addNisn'],
            'username' => $post['addUsername'],
            'jurusan'  => $post['addJurusan'],
            'nomor_hp' => $post['addTelp'],
            'foto' => $post['foto'],
            'password' => md5($post['addPassword']),
        );
        $nisn = $post['addNisn'];
        $id = array('id' => $post['idRegister']);
        $dataProgram = array(
            'kode_instansi' => $post['addInstansi'],
            'kode_program' => $post['addProgram']
        );
        $query = $this->ProfileModel->insertUserSiswa('tb_siswa', $data, $nisn, $dataProgram, $id);
        if ($query != false) {
            $this->session->set_flashdata('succ', 'Berhasil menambah siswa');
            redirect('Profile');
        }else {
            $this->session->set_flashdata('fail', 'Username sudah dipakai');
            redirect('Profile');
        }
    }
}
