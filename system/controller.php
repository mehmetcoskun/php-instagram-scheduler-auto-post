<?php
date_default_timezone_set("Europe/Istanbul");
require_once "database.php";
require_once "function.php";
require_once __DIR__ . "/Netrassgram.php";
$netrassgram = new Netrassgram();

//Signin
if (isset($_POST["signin_hidden"])) {
    $netrassgram->loginInstagram($_POST["username"], $_POST["password"]);
}

//Create Schedule
if (isset($_POST["createschedule_hidden"])) {
    $media = $_FILES["media"];
    $description = $_POST["description"];
    $date = date("d.m.Y", strtotime($_POST["date"]));
    $time = $_POST["time"];

    if ($date >= date("d.m.Y", strtotime("now"))) {
        if ($date == date("d.m.Y", strtotime("now"))) {
            if ($time >= date("H:i", strtotime("now"))) {
                $medias = [];
                for ($i = 0; $i < count($media["tmp_name"]); $i++) {
                    $types = array("image/jpeg", "image/png", "video/mp4");
                    if (!in_array($media["type"][$i], $types)) {
                        $data["status"] = "error";
                        $data["reason"] = "Dosya türü jpg, png veya mp4 olmak zorunda";
                        echo json_encode($data);
                    } else {
                        $extension = explode(".", $media["name"][$i]);
                        $extension = $extension[count($extension) - 1];
                        $medianame = uniqid() . "." . $extension;
                        $mediadestination = "userfiles/" . $medianame;
                        $type = $extension == "mp4" ? "video" : "photo";
                        copy($media["tmp_name"][$i], $mediadestination);

                        $medias[] = $medianame;
                    }
                }

                $scheduleinsert = $db->prepare("INSERT INTO schedules SET
                    username = ?,
                    password = ?,
                    mediasname = ?,
                    description = ?,
                    date = ?,
                    time = ?");
                $scheduleinsert->execute([
                    $_SESSION["username"],
                    $_SESSION["password"],
                    implode(",", $medias),
                    $description,
                    $date,
                    $time
                ]);

                if ($scheduleinsert) {
                    $data["status"] = "success";
                    $data["reason"] = "Gönderi Başarıyla Planlandı";
                    echo json_encode($data);
                }
            } else {
                $data["status"] = "error";
                $data["reason"] = "Geçersiz Zaman Dilimi";
                echo json_encode($data);
            }
        } else {
            $medias = [];
            for ($i = 0; $i < count($media["tmp_name"]); $i++) {
                $types = array("image/jpeg", "image/png", "video/mp4");
                if (!in_array($media["type"][$i], $types)) {
                    $data["status"] = "error";
                    $data["reason"] = "Dosya türü jpg, png veya mp4 olmak zorunda";
                    echo json_encode($data);
                } else {
                    $extension = explode(".", $media["name"][$i]);
                    $extension = $extension[count($extension) - 1];
                    $medianame = uniqid() . "." . $extension;
                    $mediadestination = "userfiles/" . $medianame;
                    $type = $extension == "mp4" ? "video" : "photo";
                    copy($media["tmp_name"][$i], $mediadestination);

                    $medias[] = $medianame;
                }
            }

            $scheduleinsert = $db->prepare("INSERT INTO schedules SET
                username = ?,
                password = ?,
                mediasname = ?,
                description = ?,
                date = ?,
                time = ?");
            $scheduleinsert->execute([
                $_SESSION["username"],
                $_SESSION["password"],
                implode(",", $medias),
                $description,
                $date,
                $time
            ]);

            if ($scheduleinsert) {
                $data["status"] = "success";
                $data["reason"] = "Gönderi Başarıyla Planlandı";
                echo json_encode($data);
            }
        }
    } else {
        $data["status"] = "error";
        $data["reason"] = "Geçersiz Tarih Dilimi";
        echo json_encode($data);
    }
}

//Post Delete
if (isset($_POST["scheduledelete"])) {
    $id = decryptId($_POST["scheduledelete"]);

    $schedule = $db->prepare("SELECT * FROM schedules WHERE id = ?");
    $schedule->execute([$id]);

    $scheduleget = $schedule->fetch(PDO::FETCH_ASSOC);

    $mediasname = explode(",", $scheduleget["mediasname"]);
    if (sizeof($mediasname) > 1) {
        for ($i = 0; $i < sizeof($mediasname); $i++) {
            unlink("userfiles/" . $mediasname[$i]);
        }
    } else {
        unlink("userfiles/" . $mediasname[0]);
    }

    $scheduledelete = $db->prepare("DELETE FROM schedules WHERE id = ?");
    $scheduledelete->execute([$id]);
}