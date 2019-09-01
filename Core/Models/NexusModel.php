<?php
class NexusModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function GetData()
    {
        $path = dirname($_SERVER["SCRIPT_FILENAME"]);
        $res = [];

        $blacklist = [
            ".",
            "..",
            ".git",
            ".gitignore",
            ".htaccess",
            "index.php",
            "LICENSE",
            "novus.config.php",
            "README.md",
            "assets",
            "config",
            "Core"
        ];

        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if(in_array($file, $blacklist)) {
                        continue;
                    }

                    $stats = stat($file);

                    $res[] = [
                        "name" => $file,
                        "type" => filetype($file),
                        "size" => $stats[7],
                        "mtime" => $stats[9],
                        "atime" => $stats[8]
                    ];
                }
                closedir($dh);
            }
        }

        return json_encode($res);
    }

    public function GetContent()
    {
        $data = $this->GetData();

        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=600');
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Encoding: gzip');
        header('Transfer-Encoding: gzip');

        ob_start('ob_gzhandler');

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
