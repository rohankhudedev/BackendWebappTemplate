<?php

class DB
{

    public function __construct()
    {
        require_once("resources.php");
        $dbHost     = G_DB_HOST;
        $dbUsername = G_DB_USER;
        $dbPassword = G_DB_PASSWORD;
        $dbName     = G_DB_NAME;
        if( !isset($this->db) )
        {
            // Connect to the database
            try
            {
                $conn     = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUsername, $dbPassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db = $conn;
            } catch( PDOException $e )
            {
                die("Failed to connect with MySQL: " . $e->getMessage());
            }
        }
    }

    /**
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */
    public function getRows( $table, $conditions = array() )
    {
        $sql = 'SELECT ';
        $sql .= array_key_exists("select", $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM ' . $table;
        if( array_key_exists("where", $conditions) )
        {
            $sql .= ' WHERE ';
            $i   = 0;
            foreach( $conditions['where'] as $key => $value )
            {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }

        if( array_key_exists("order_by", $conditions) )
        {
            $sql .= ' ORDER BY ' . $conditions['order_by'];
        }

        if( array_key_exists("start", $conditions) && array_key_exists("limit", $conditions) )
        {
            $sql .= ' LIMIT ' . $conditions['start'] . ',' . $conditions['limit'];
        }
        elseif( !array_key_exists("start", $conditions) && array_key_exists("limit", $conditions) )
        {
            $sql .= ' LIMIT ' . $conditions['limit'];
        }

        $query = $this->db->prepare($sql);
        $query->execute();

        if( array_key_exists("return_type", $conditions) && $conditions['return_type'] != 'all' )
        {
            switch( $conditions['return_type'] )
            {
                case 'count':
                    $data = $query->rowCount();
                    break;
                case 'single':
                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    break;
                default:
                    $data = '';
            }
        }
        else
        {
            if( $query->rowCount() > 0 )
            {
                $data = $query->fetchAll();
            }
        }
        return !empty($data) ? $data : false;
    }

    /**
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function insert( $table, $data )
    {
        if( !empty($data) && is_array($data) )
        {
            $columns = '';
            $values  = '';
            $i       = 0;
            date_default_timezone_set("Asia/Kolkata");
            if( !array_key_exists('created', $data) )
            {
                $data['created'] = date("Y-m-d H:i:s");
            }
            if( !array_key_exists('modified', $data) )
            {
                $data['modified'] = date("Y-m-d H:i:s");
            }

            $columnString = implode(',', array_keys($data));
            $valueString  = ":" . implode(',:', array_keys($data));
            $sql          = "INSERT INTO " . $table . " (" . $columnString . ") VALUES (" . $valueString . ")";
            $query        = $this->db->prepare($sql);
            foreach( $data as $key => $val )
            {
                $query->bindValue(':' . $key, $val);
            }
            $insert = $query->execute();
            return $insert ? $this->db->lastInsertId() : false;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update data into the database
     * @param string name of the table
     * @param array the data for updating into the table
     * @param array where condition on updating data
     */
    public function update( $table, $data, $conditions = array() )
    {
        if( !empty($data) && is_array($data) )
        {
            $colvalSet = '';
            $whereSql  = '';
            $i         = 0;
            date_default_timezone_set("Asia/Kolkata");
            if( !array_key_exists('modified', $data) )
            {
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach( $data as $key => $val )
            {
                $pre       = ($i > 0) ? ', ' : '';
                $colvalSet .= $pre . $key . "='" . $val . "'";
                $i++;
            }
            if( !empty($conditions) && is_array($conditions) )
            {
                $whereSql .= ' WHERE ';
                $i        = 0;
                foreach( $conditions as $key => $value )
                {
                    $pre      = ($i > 0) ? ' AND ' : '';
                    $whereSql .= $pre . $key . " = '" . $value . "'";
                    $i++;
                }
            }
            $sql    = "UPDATE " . $table . " SET " . $colvalSet . $whereSql;
            $query  = $this->db->prepare($sql);
            $update = $query->execute();
            return $update ? $query->rowCount() : false;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete( $table, $conditions )
    {
        $whereSql = '';
        if( !empty($conditions) && is_array($conditions) )
        {
            $whereSql .= ' WHERE ';
            $i        = 0;
            foreach( $conditions as $key => $value )
            {
                $pre      = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $sql    = "DELETE FROM " . $table . $whereSql;
        $delete = $this->db->exec($sql);
        return $delete ? $delete : false;
    }

    public function escapeString( $unescaped )
    {
        //source - https://stackoverflow.com/questions/1162491/alternative-to-mysql-real-escape-string-without-connecting-to-db
        // \xC2\xA0 is the no-break space
        $unescaped    = strip_tags(trim(html_entity_decode($unescaped, ENT_QUOTES, 'UTF-8'), "\n\r\t\xC2\xA0"));
//        $unescaped    = stripslashes($unescaped);
//        $unescaped    = addslashes($unescaped);
        $replacements = array(
            "\x00" => '\\0',
            "\n"   => '\\n',
            "\r"   => '\\r',
            "\\"   => '\\\\',
            "'"    => "\'",
            '"'    => '\"',
            "\x1a" => '\\Z'
        );
        return trim(strtr($unescaped, $replacements));
    }

    /**
     * uploads file to upload directory
     * @param file $_FILES['tag name attribute']
     * @return string saved filename
     */
    public function uploadImage( $file, $oldfile = NULL )
    {
        /**
         * Three cases get handle here
         * 1. inital case - when user provides new entry of image and old_image is empty - image(not empty) ; old_image(empty)
         * 2. updating case - when user dont provide image to update but old_image is not empty - image(empty); old_image(not empty)
         * 3. updating case - when user provide image to update and old_image is not empty - image(not empty); old_image(not empty)
         */
        if( !empty($file["tmp_name"]) )
        {
            if( !is_null($oldfile) && !empty($oldfile) )
            {
                //remove previous file
                $oldfile = 'uploads/' . $oldfile;
                @unlink($oldfile);
            }

            //$file=$_FILES["banner_img"]
            $tmp_name   = $file["tmp_name"];
            $namefile   = $file["name"];
            $ref        = explode(".", $namefile);
            $ext        = end($ref);
            $image_name = time() . "." . $ext;
            move_uploaded_file($tmp_name, "uploads/" . $image_name);
            sleep(1); //sleep for 1 second to upload next immediate image
            return $image_name;
        }
        return $oldfile;
    }

    /**
     * Delete data from the database
     * @param string subject
     * @param string to address
     * @param string from address
     * @param string body
     * @return boolean
     */
    public function sendMail( $subject, $to, $from, $body )
    {

        require_once("PHPMailer/class.phpmailer.php");
        require_once("PHPMailer/class.smtp.php");
        $mail             = new PHPMailer();
        $mail->IsSMTP(); // enable SMTP
        // FIXME: comment for production
        //$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        // 3 = Enable verbose debug output
        $mail->SMTPAuth   = true; // enable SMTP authentication
        //$mail->SMTPSecure = 'ssl';                          // Enable TLS encryption, `ssl` also accepted
        //$mail->Host = "smtp.gmail.com";
        $mail->Host       = G_SMTP_HOST;
        $mail->Port       = G_SMTP_PORT; // or 587 or 465
        $mail->Username   = G_SMTP_USER;
        $mail->Password   = G_SMTP_PWD;
        $mail->Subject    = $subject;
        $mail->SetFrom(G_SMTP_FROM,"ETYL B-School 2017");
        $mail->AddAddress($to);
        $mail->Body       = $body;
        $mail->IsHTML(true);
        if( !$mail->Send() )
        {
            error_log("Mail Error - " . $mail->ErrorInfo);
            return false;
        }
        else
        {
            return true;
        }
    }

}
