<?php
class hostingSpaceDetector extends Plugin
{

    public function init()
    {
        // Fields and default values for the database of this plugin
        $this->dbFields = array(
            'mblimit' => 0,
            'email' => '',
            'danger' => false,
            'sended' => false
        );
    }

    public function form()
    {

        global $L;
        $email = $this->getValue('email');
        $MBlimit = $this->getValue('mblimit');

        $dir = PATH_ROOT;
        function folderSize($dir)
        {
            $size = 0;
            foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each)
            {
                $size += is_file($each) ? filesize($each) : folderSize($each);
            }

            return $size;
        }
        $bytes = folderSize($dir);
        function formatSizeUnits($bytes)
        {
            if ($bytes >= 1048576)
            {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            }
            elseif ($bytes >= 1024)
            {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            }
            elseif ($bytes > 1)
            {
                $bytes = $bytes . ' bytes';
            }
            elseif ($bytes == 1)
            {
                $bytes = $bytes . ' byte';
            }
            else
            {
                $bytes = '0 bytes';
            }

            return $bytes;
        };

        $sizeWebsite = formatSizeUnits($bytes);
        $sizeWebsiteValue = str_replace(' MB', '', $sizeWebsite);

        if ($MBlimit)
        {
            $sizeWebsiteWithoutMB = str_replace(' MB', '', $sizeWebsite);
            $limitLine = $sizeWebsiteWithoutMB / $MBlimit * 100;
        }
        else
        {
            $sizeWebsite = 0;
            $limitLine = 0;
        };

        echo ' 
<br>
        <label for="file"><b>' . $L->get('websiteuses') . '</b></label>';

        echo $sizeWebsite . '/<b>' . $MBlimit . ' MB<b><br>
<div style="width:100%;height:30px;background:#fff;border:solid 1px #ddd;margin-top:5px;overflow:hidden;"><div class="" style="width:' . $limitLine . '%;background:red;height:30px;"></div></div>
<label for="limit">' . $L->get('info') . '</label>
<input type="text" name="mblimit" placeholder="' . $L->get('placeholdermb') . '"
value="' . $MBlimit . '" class="form-control">
 <label for="email">' . $L->get('spaceover') . '</label>
<input type="email" value="' . $email . '" placeholder="example@example.com" name="email" 
class="form-control"/>';

    }

    public function adminBodyBegin()
    {

        $email = $this->getValue('email');


        $MBlimit = $this->getValue('mblimit');
        $limitPercent95 = $MBlimit * 0.95;
        global $L;
        $dir = PATH_ROOT;

        function folderSizez($dir)
        {
            $size = 0;
            foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each)
            {
                $size += is_file($each) ? filesize($each) : folderSizez($each);
            }
            return $size;
        }
        $bytes = folderSizez($dir);
        function formatSizeUnitz($bytes)
        {
            if ($bytes >= 1048576)
            {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            }
            elseif ($bytes >= 1024)
            {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            }
            elseif ($bytes > 1)
            {
                $bytes = $bytes . ' bytes';
            }
            elseif ($bytes == 1)
            {
                $bytes = $bytes . ' byte';
            }
            else
            {
                $bytes = '0 bytes';
            }

            return $bytes;
        };

        $sizeWebsite = formatSizeUnitz($bytes);
        $sizeWebsiteValue = str_replace(' MB', '', $sizeWebsite);



        ///



        $folder = PATH_CONTENT . 'hostingSpaceDetector/';
        $file = $folder . 'mailsended.txt';
        $chmod = 0755;
        $fileExist = file_exists($folder) || mkdir($folder, $chmod);


        ///



        if ($sizeWebsiteValue > $limitPercent95 && !$MBlimit == 0)
        {

            echo '<div class="alert alert-danger" role="alert">' . $L->get('out-of-space') . '</div>';
            $subject = $L->get('subject');
            $message = DOMAIN .''.$L->get('messagemail');

            if (!file_exists($file))
            {


                mail($email, $subject, $message);

                if ($fileExist)
                {
                    file_put_contents($file, '');
                };
            };

        };

        if ($sizeWebsiteValue < $limitPercent95)
        {

            if (file_exists($file))
            {
                unlink($file);
            }

        }

    }

}
?>