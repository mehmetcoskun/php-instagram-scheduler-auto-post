<?php
date_default_timezone_set("Europe/Istanbul");
session_start();

require_once __DIR__ . "/vendor/autoload.php";

/**
 *      _   _      _
 *     | \ | |    | |
 *     |  \| | ___| |_ _ __ __ _ ___ ___  __ _ _ __ __ _ _ __ ___
 *     | . ` |/ _ \ __| '__/ _` / __/ __|/ _` | '__/ _` | '_ ` _ \
 *     | |\  |  __/ |_| | | (_| \__ \__ \ (_| | | | (_| | | | | | |
 *     |_| \_|\___|\__|_|  \__,_|___/___/\__, |_|  \__,_|_| |_| |_|
 *                                        __/ |
 *                                       |___/
 *
 *  Copyright (C) 2019 Mehmet COŞKUN
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

use InstagramAPI\Instagram;

class Netrassgram
{

    /** @var Instagram */
    private $instagram;

    /** @var PDO */
    private $db;

    /** @var null */
    private $id = null;

    public function __construct()
    {
        $json = json_decode(file_get_contents(__DIR__ . "/database.json"));

        try {
            $this->db = new PDO("mysql:host=" . $json->host . ";dbname=" . $json->dbname . ";charset=" . $json->dbchar . ";", $json->dbuser, $json->dbpass);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $username
     * @param $password
     */
    public function loginInstagram($username, $password)
    {
        \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $this->instagram = new \InstagramAPI\Instagram();

        try {
            $this->instagram->login($username, $password);

            $_SESSION["status"] = true;
            $_SESSION["fullname"] = $this->instagram->account->getCurrentUser()->getUser()->getFullName();
            $_SESSION["profile_picture"] = $this->instagram->account->getCurrentUser()->getUser()->getProfilePicUrl();
            $_SESSION["username"] = $username;
            $_SESSION["password"] = $password;

            $data["status"] = "success";
            $data["reason"] = "Giriş Yapıldı.";
            echo json_encode($data);
        } catch (Exception $e) {
            $data["status"] = "error";
            $data["reason"] = "Girdiğiniz şifre yanlış. Lütfen tekrar deneyin.";
            echo json_encode($data);
        }
    }

    public function checkSchedule()
    {
        \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $this->instagram = new \InstagramAPI\Instagram();
        \InstagramAPI\Utils::$ffprobeBin = __DIR__ . "/FFmpeg/bin/ffprobe.exe"; //Your FFprobe path
        \InstagramAPI\Media\Video\FFmpeg::$defaultBinary =  __DIR__ . "/FFmpeg/bin/ffmpeg.exe"; //Your FFmpeg path

        try {
            $schedules = $this->db->prepare("SELECT * FROM schedules");
            $schedules->execute();

            while ($schedulesget = $schedules->fetch(PDO::FETCH_ASSOC)) {
                $this->id = $schedulesget["id"];

                $this->instagram->login($schedulesget["username"], $schedulesget["password"]);

                if ($schedulesget["date"] == date("d.m.Y", strtotime("now")) && $schedulesget["time"] == date("H:i", strtotime("now"))) {
                    $mediascount = explode(",", $schedulesget["mediasname"]);
                    if (sizeof($mediascount) > 1) {

                        $medias = explode(",", $schedulesget["mediasname"]);

                        $arr = [];
                        foreach ($medias as $media) {
                            $extension = explode(".", $media);
                            $extension = $extension[count($extension) - 1];
                            $type = $extension == "mp4" ? "video" : "photo";

                            $values = [
                                "type" => $type,
                                "file" => __DIR__ . "/userfiles/" . $media
                            ];

                            array_push($arr, $values);
                        }

                        $mediaOptions = [
                            'targetFeed' => \InstagramAPI\Constants::FEED_TIMELINE_ALBUM,
                        ];
                        foreach ($arr as &$item) {
                            $validMedia = null;
                            switch ($item['type']) {
                                case 'photo':
                                    $validMedia = new \InstagramAPI\Media\Photo\InstagramPhoto($item['file'], $mediaOptions);
                                    break;
                                case 'video':
                                    $validMedia = new \InstagramAPI\Media\Video\InstagramVideo($item['file'], $mediaOptions);
                                    break;
                                default:
                            }
                            if ($validMedia === null) {
                                continue;
                            }
                            try {
                                $item['file'] = $validMedia->getFile();
                                $item['__media'] = $validMedia;
                            } catch (\Exception $e) {
                                continue;
                            }
                            if (!isset($mediaOptions['forceAspectRatio'])) {
                                $mediaDetails = $validMedia instanceof \InstagramAPI\Media\Photo\InstagramPhoto
                                    ? new \InstagramAPI\Media\Photo\PhotoDetails($item['file'])
                                    : new \InstagramAPI\Media\Video\VideoDetails($item['file']);
                                $mediaOptions['forceAspectRatio'] = $mediaDetails->getAspectRatio();
                            }
                        }
                        unset($item);

                        $this->instagram->timeline->uploadAlbum($arr, ['caption' => $schedulesget["description"]]);

                        $schedulesupdate = $this->db->prepare("UPDATE schedules SET status = ? WHERE id = ?");
                        $schedulesupdate->execute(["success", $schedulesget["id"]]);
                    } else {
                        $extension = explode(".", $schedulesget["mediasname"]);
                        $extension = $extension[count($extension) - 1];
                        $type = $extension == "mp4" ? "video" : "photo";

                        if ($type == "photo") {
                            $photo = new \InstagramAPI\Media\Photo\InstagramPhoto(__DIR__ . "/userfiles/" . $schedulesget["mediasname"]);
                            $this->instagram->timeline->uploadPhoto($photo->getFile(), ['caption' => $schedulesget["description"]]);
                        } elseif($type == "video") {
                            $video = new \InstagramAPI\Media\Video\InstagramVideo(__DIR__ . "/userfiles/" . $schedulesget["mediasname"]);
                            $this->instagram->timeline->uploadVideo($video->getFile(), ['caption' => $schedulesget["description"]]);
                        }

                        $schedulesupdate = $this->db->prepare("UPDATE schedules SET status = ? WHERE id = ?");
                        $schedulesupdate->execute(["success", $schedulesget["id"]]);
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();

            $schedulesupdate = $this->db->prepare("UPDATE schedules SET status = ? WHERE id = ?");
            $schedulesupdate->execute(["error", $this->id]);
        }
    }
}