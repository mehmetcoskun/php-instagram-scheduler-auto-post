<?php
function encryptId($id)
{
    return base64_encode(base64_encode(base64_encode(base64_encode($id))));
}

function decryptId($id)
{
    return base64_decode(base64_decode(base64_decode(base64_decode($id))));
}
?>