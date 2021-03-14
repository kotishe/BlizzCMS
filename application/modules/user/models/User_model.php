<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    /**
     * User_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->load->database('auth', TRUE);
    }

    /**
     * @param mixed $oldpass
     * @param mixed $newpass
     * @param mixed $renewpass
     * 
     * @return [type]
     */
    public function changePassword($oldpass, $newpass, $renewpass)
    {
        $passnobnet = $this->wowauth->Account($this->session->userdata('wow_sess_username'), $oldpass);
        $passbnet = $this->wowauth->Battlenet($this->session->userdata('wow_sess_email'), $oldpass);
        $newaccpass = $this->wowauth->Account($this->session->userdata('wow_sess_username'), $newpass);
        $newaccbnetpass = $this->wowauth->Battlenet($this->session->userdata('wow_sess_email'), $newpass);

        if($this->wowgeneral->getExpansionAction() == 1) {
            if($this->wowgeneral->getEmulatorAction() == 1) {
                if ($this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id')) == strtoupper($passbnet)) {
                    if ($newaccbnetpass == $this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id'))) {
                        return 'samePass';
                    }
                    else
                        if(strlen($newpass) >= 5 && strlen($newpass) <= 16) {
                            if($newpass == $renewpass) {
                                $change = array(
                                    'sha_pass_hash' => $newaccpass,
                                    'sessionkey' => '',
                                    'v' => '',
                                    's' => ''
                                );
    
                                $this->auth->where('id', $this->session->userdata('wow_sess_id'))->update('account', $change);
    
                                $this->auth->set('sha_pass_hash', $newaccbnetpass)->where('id', $this->session->userdata('wow_sess_id'))->update('battlenet_accounts');
                                return true;
                            }
                            else
                                return 'noMatch';
                        }
                        else
                            return 'lengError';
                }
                else
                    return 'passnotMatch';
            }
            else
            {
                if ($this->wowauth->getPasswordAccountID($this->session->userdata('wow_sess_id')) == strtoupper($passnobnet)) {
                    if($newaccpass == $this->wowauth->getPasswordAccountID($this->session->userdata('wow_sess_id'))) {
                        return 'samePass';
                    }
                    else
                        if(strlen($newpass) >= 5 && strlen($newpass) <= 16) {
                            if ($newpass == $renewpass) {
                                    $change = array(
                                        'sha_pass_hash' => $newaccpass,
                                        'sessionkey' => '',
                                        'v' => '',
                                        's' => ''
                                    );
    
                                    $this->auth->where('id', $this->session->userdata('wow_sess_id'))->update('account', $change);
                                    return true;
                            }
                            else
                                return 'noMatch';
                        }
                        else
                            return 'lengError';
                }
                else
                    return 'passnotMatch';
            }
        }
        elseif($this->wowgeneral->getExpansionAction() == 2) {
            if ($this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id')) == strtoupper($passbnet)) {
                if ($newaccbnetpass == $this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id'))) {
                    return 'samePass';
                }
                else
                    if(strlen($newpass) >= 5 && strlen($newpass) <= 16) {
                        if($newpass == $renewpass) {
                            $change = array(
                                'sha_pass_hash' => $newaccpass,
                                'sessionkey' => '',
                                'v' => '',
                                's' => ''
                            );

                            $this->auth->where('id', $this->session->userdata('wow_sess_id'))->update('account', $change);

                            $this->auth->set('sha_pass_hash', $newaccbnetpass)->where('id', $this->session->userdata('wow_sess_id'))->update('battlenet_accounts');
                            return true;
                        }
                        else
                            return 'noMatch';
                    }
                    else
                        return 'lengError';
            }
            else
                return 'passnotMatch';
        }
        else
            return 'expError';
    }

    /**
     * @param mixed $newemail
     * @param mixed $renewemail
     * @param mixed $password
     * 
     * @return [type]
     */
    public function changeEmail($newemail, $renewemail, $password)
    {
        $nobnet = $this->wowauth->Account($this->session->userdata('wow_sess_username'), $password);
        $bnet = $this->wowauth->Battlenet($this->session->userdata('wow_sess_email'), $password);
        $newbnetpass = $this->wowauth->Battlenet($newemail, $password);

        if($this->wowgeneral->getExpansionAction() == 1) {
            if($this->wowgeneral->getEmulatorAction() == 1) {
                if ($this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id')) == strtoupper($bnet)) {
                    if($newemail == $renewemail) {
                        if($this->getExistEmail(strtoupper($newemail)) > 0) {
                            return 'usedEmail';
                        }
                        else
                            $this->auth->set('email', $newemail)->where('id', $this->session->userdata('wow_sess_id'))->update('account');
    
                            $this->db->set('email', $newemail)->where('id', $this->session->userdata('wow_sess_id'))->update('users');
    
                            $update = array(
                                'sha_pass_hash' => $newbnetpass,
                                'email' => $newemail
                            );
    
                            $this->auth->where('id', $this->session->userdata('wow_sess_id'))->update('battlenet_accounts', $update);
                            return true;
                    }
                    else
                        return 'enoMatch';
                }
                else
                    return 'epassnotMatch';
            }
            else
            {
                if ($this->wowauth->getPasswordAccountID($this->session->userdata('wow_sess_id')) == strtoupper($nobnet)) {
                    if($newemail == $renewemail) {
                        if($this->getExistEmail(strtoupper($newemail)) > 0) {
                            return 'usedEmail';
                        }
                        else
                            $this->auth->set('email', $newemail)->where('id', $this->session->userdata('wow_sess_id'))->update('account');

                            $this->db->set('email', $newemail)->where('id', $this->session->userdata('wow_sess_id'))->update('users');
                            return true;
                    }
                    else
                        return 'enoMatch';
                }
                else
                    return 'epassnotMatch';
            }
        }
        elseif($this->wowgeneral->getExpansionAction() == 2) {
            if ($this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id')) == strtoupper($bnet)) {
                if($newemail == $renewemail) {
                    if($this->getExistEmail(strtoupper($newemail)) > 0) {
                        return 'usedEmail';
                    }
                    else
                        $this->auth->set('email', $newemail)->where('id', $this->session->userdata('wow_sess_id'))->update('account');

                        $this->db->set('email', $newemail)->where('id', $this->session->userdata('wow_sess_id'))->update('users');

                        $update = array(
                            'sha_pass_hash' => $newbnetpass,
                            'email' => $newemail
                        );

                        $this->auth->where('id', $this->session->userdata('wow_sess_id'))->update('battlenet_accounts', $update);
                        return true;
                }
                else
                    return 'enoMatch';
            }
            else
                return 'epassnotMatch';
        }
        else
            return 'expaError';
    }

    /**
     * @param mixed $email
     * 
     * @return [type]
     */
    public function getExistEmail($email)
    {
        return $this->auth->select('email')->where('email', $email)->get('account')->num_rows();
    }

    /**
     * @return [type]
     */
    public function getAllAvatars()
    {
        return $this->db->select('*')->order_by('id ASC')->get('avatars');
    }

    /**
     * @param mixed $avatar
     * 
     * @return [type]
     */
    public function changeAvatar($avatar)
    {
        $this->db->set('profile', $avatar)->where('id', $this->session->userdata('wow_sess_id'))->update('users');
        return true;
    }

    /**
     * @param mixed $id
     * 
     * @return [type]
     */
    public function getDateMember($id)
    {
        $qq = $this->db->select('joindate')->where('id', $id)->get('users');

        if ($qq->num_rows())
            return $qq->row('joindate');
        else
            return 'Unknow';
    }

    /**
     * @param mixed $id
     * 
     * @return [type]
     */
    public function getExpansion($id)
    {
        $qq = $this->db->select('expansion')->where('id', $id)->get('users');

        if ($qq->num_rows())
            return $qq->row('expansion');
        else
            return 'Unknow';
    }

    /**
     * @param mixed $id
     * 
     * @return [type]
     */
    public function getLastIp($id)
    {
        return $this->auth->select('last_ip')->where('id', $id)->get('account')->row('last_ip');
    }

    /**
	 * Check if user exists
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function find_user($id)
	{
		$query = $this->db->where('id', $id)->get('users')->num_rows();

		return ($query == 1);
	}


    /**
     * @param mixed $username
     * @param mixed $password
     * 
     * @return [type]
     */
    public function authentication($username, $password)
    {
        $accgame =  $this->auth->where('username', $username)->or_where('email', $username)->get('account')->row();
        $emulator = $this->config->item('emulator');

        if (empty($accgame))
		{
			return false;
		}

        switch ($emulator)
        {
            case 'srp6':
				$validate = ($accgame->verifier === $this->wowauth->game_hash($accgame->username, $password, 'srp6', $accgame->salt));
				break;
			case 'hex':
				$validate = (strtoupper($accgame->v) === $this->wowauth->game_hash($accgame->username, $password, 'hex', $accgame->s));
				break;
			case 'old_trinity':
				$validate = hash_equals(strtoupper($accgame->sha_pass_hash), $this->wowauth->game_hash($accgame->username, $password));
				break;
        }

		if (! isset($validate) || ! $validate)
		{
			return false;
		}

		// if account on website don't exist sync values from game account
		if (! $this->find_user($accgame->id))
		{
			$this->db->insert('users', [
				'id'        => $accgame->id,
				'nickname'  => $accgame->username,
				'username'  => $accgame->username,
				'email'     => $accgame->email,
				'joined_at' => strtotime($accgame->joindate)
			]);
		}

        $this->wowauth->arraySession($accgame->id);
    
    }
    
    /**
     * @param mixed $username
     * @param mixed $email
     * @param mixed $password
     * @param mixed $emulator
     * 
     * @return [type]
     */
    public function insertRegister($username, $email, $password, $emulator)
    {
        $date = $this->wowgeneral->getTimestamp();
        $expansion = $this->wowgeneral->getRealExpansionDB();
        $emulator = $this->config->item('emulator');


        if ($emulator == "srp6")
        {
            $salt = random_bytes(32);

            $data = array(
                'username'  => $username,
                'salt'      => $salt,
                'verifier' => $this->wowauth->game_hash($username, $password, 'srp6', $salt),
                'email'     => $email,
                'expansion' => $expansion,
                'session_key_auth' => null,
                'session_key_bnet' => null
            );

            $this->auth->insert('account', $data);

        }
        elseif ($emulator == "hex")
        {
            $salt = strtoupper(bin2hex(random_bytes(32)));

            $data = array(
                'username'  => $username,
                'v'          => $this->wowauth->game_hash($username, $password, 'hex', $salt),
                's'          => $salt,
                'email'     => $email,
                'expansion' => $expansion,
            );

            $this->auth->insert('account', $data);
        }

        elseif ($emulator == "old-trinity")
        {
            $data = array(
                'username'  => $username,
                'sha_pass_hash' => $this->wowauth->game_hash($username, $password),
                'email'     => $email,
                'expansion' => $expansion,
                'sessionkey'    => '',
            );

            $this->auth->insert('account', $data);
        }

        $id = $this->wowauth->getIDAccount($username);

        if ($this->config->item('bnet_enabled'))
        {

            $data1 = array(
                'id' => $id,
                'email' => $email,
                'sha_pass_hash' => $this->wowauth->game_hash($email, $password, 'bnet')
            );

            $this->auth->insert('battlenet_accounts', $data1);

            $this->auth->set('battlenet_account', $id)->where('id', $id)->update('account');
            $this->auth->set('battlenet_index', '1')->where('id', $id)->update('account');

        }

        $website = array(
            'id' => $id,
            'username' => $username,
            'email' => $email,
            'joindate' => $date,
            'dp' => 0,
            'vp' => 0
        );

        $this->db->insert('users', $website);

        return true;

    }

    /**
     * @param mixed $username
     * 
     * @return [type]
     */
    public function checkuserid($username)
    {
        return $this->auth->select('id')->where('username', $username)->get('account')->row('id');
    }

    /**
     * @param mixed $email
     * 
     * @return [type]
     */
    public function checkemailid($email)
    {
        return $this->auth->select('id')->where('email', $email)->get('account')->row('id');
    }

    /**
     * @param mixed $username
     * @param mixed $email
     * 
     * @return [type]
     */
    public function sendpassword($username, $email)
    {
        $ucheck = $this->checkuserid($username);
        $echeck = $this->checkemailid($email);

        if ($ucheck == $echeck)
        {
            $allowed_chars = "0123456789abcdefghijklmnopqrstuvwxyz";
            $password_generated = "";
            $password_generated = substr(str_shuffle($allowed_chars), 0, 14);
            $newpass = $password_generated;
            $newpassI = $this->wowauth->Account($username, $newpass);
            $newpassII = $this->wowauth->Battlenet($email, $newpass);

            if ($this->wowgeneral->getExpansionAction() == 1)
            {
                $accupdate = array(
                    'sha_pass_hash' => $newpassI,
                    'sessionkey' => '',
                    'v' => '',
                    's' => ''
                );

                $this->auth->where('id', $ucheck)->update('account', $accupdate);
            }
            else
            {
                $accupdate = array(
                    'sha_pass_hash' => $newpassI,
                    'sessionkey' => '',
                    'v' => '',
                    's' => ''
                );

                $this->auth->where('id', $ucheck)->update('account', $accupdate);

                $this->auth->set('sha_pass_hash', $newpassII)->where('id', $echeck)->update('battlenet_accounts');
            }

            $mail_message = 'Hi, <span style="font-weight: bold;text-transform: uppercase;">'.$username.'</span> You have sent a request for your account password to be reset.<br>';
            $mail_message .= 'Your new password is: <span style="font-weight: bold;">'.$password_generated.'</span><br>';
            $mail_message .= 'Please change your password again as soon as you log in!<br>';
            $mail_message .= 'Kind regards,<br>';
            $mail_message .= $this->config->item('email_settings_sender_name').' Support.';

            return $this->wowgeneral->smtpSendEmail($email, $this->lang->line('email_password_recovery'), $mail_message);
        }
        else
            return 'sendErr';
    }

    /**
     * @param mixed $account
     * 
     * @return [type]
     */
    public function getIDPendingUsername($account)
    {
        return $this->db->select('id')->where('username', $account)->get('pending_users')->num_rows();
    }

    /**
     * @param mixed $email
     * 
     * @return [type]
     */
    public function getIDPendingEmail($email)
    {
        return $this->db->select('id')->where('email', $email)->get('pending_users')->num_rows();
    }

    /**
     * @param mixed $key
     * 
     * @return [type]
     */
    public function checkPendingUser($key)
    {
        return $this->db->select('id')->where('key', $key)->get('pending_users')->num_rows();
    }

    /**
     * @param mixed $key
     * 
     * @return [type]
     */
    public function getTempUser($key)
    {
        return $this->db->select('*')->where('key', $key)->get('pending_users')->row_array();
    }

    /**
     * @param mixed $key
     * 
     * @return [type]
     */
    public function removeTempUser($key)
    {
        return $this->db->where('key', $key)->delete('pending_users');
    }

    /**
     * @param mixed $key
     * 
     * @return [type]
     */
    public function activateAccount($key)
    {

        $check = $this->checkPendingUser($key);
        $temp = $this->getTempUser($key);

        if($check == "1") {
            if ($this->wowgeneral->getExpansionAction() == 1)
            {
                $data = array(
                    'username' => $temp['username'],
                    'sha_pass_hash' => $temp['password'],
                    'email' => $temp['email'],
                    'expansion' => $temp['expansion'],
                );

                $this->auth->insert('account', $data);
            }
            else
            {
                $data = array(
                    'username' => $temp['username'],
                    'sha_pass_hash' => $temp['password'],
                    'email' => $temp['email'],
                    'expansion' => $temp['expansion'],
                    'battlenet_index' => '1',
                );

                $this->auth->insert('account', $data);

                $id = $this->wowauth->getIDAccount($temp['username']);

                $data1 = array(
                    'id' => $id,
                    'email' => $temp['email'],
                    'sha_pass_hash' => $temp['password_bnet']
                );

                $this->auth->insert('battlenet_accounts', $data1);

                $this->auth->set('battlenet_account', $id)->where('id', $id)->update('account');
            }

            $id = $this->wowauth->getIDAccount($temp['username']);

            $data3 = array(
                'id' => $id,
                'username' => $temp['username'],
                'email' => $temp['email'],
                'joindate' => $temp['joindate']
            );

            $this->db->insert('users', $data3);

            $this->removeTempUser($key);

            $this->session->set_flashdata('account_activation','true');
            redirect(base_url('login'));
        }
        else
            $this->session->set_flashdata('account_activation','false');
            redirect(base_url('login'));
    }

     /**
      * @param mixed $newusername
      * @param mixed $renewusername
      * @param mixed $password
      * 
      * @return [type]
      */
     public function changeUsername($newusername, $renewusername, $password)
     {
        $nobnet = $this->wowauth->Account($this->session->userdata('wow_sess_username'), $password);
        $bnet = $this->wowauth->Battlenet($this->session->userdata('wow_sess_email'), $password);

        if($this->wowgeneral->getExpansionAction() == 1) {
            if($this->wowgeneral->getEmulatorAction() == 1) {
                if ($this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id')) == strtoupper($bnet)) {
                    if($newusername == $renewusername) {
                        $this->db->set('username', $newusername)->where('id', $this->session->userdata('wow_sess_id'))->update('users');
                        return true;
                    }
                    else
                        return 'enoMatch';
                }
                else
                    return 'epassnotMatch';
            }
            else
            {
                if ($this->wowauth->getPasswordAccountID($this->session->userdata('wow_sess_id')) == strtoupper($nobnet)) {
                    if($newusername == $renewusername) {
                        $this->db->set('username', $newusername)->where('id', $this->session->userdata('wow_sess_id'))->update('users');
                        return true;
                    }
                    else
                        return 'enoMatch';
                }
                else
                    return 'epassnotMatch';
            }
        }
        else
        {
            if ($this->wowauth->getPasswordBnetID($this->session->userdata('wow_sess_id')) == strtoupper($bnet)) {
                if($newusername == $renewusername) {
                    $this->db->set('username', $newusername)->where('id', $this->session->userdata('wow_sess_id'))->update('users');
                    return true;
                }
                else
                    return 'enoMatch';
            }
            else
                return 'epassnotMatch';
        }
     }
}
