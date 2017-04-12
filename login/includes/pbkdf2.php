<?php
function pbkdf2($password, $salt, $algorithm = 'sha512', $count = 20000, $key_length = 128, $raw_output = false) {
    if (!in_array($algorithm, hash_algos(), true)) {
        exit('Hash error');
    }

    if ($count <= 0 || $key_length <= 0) {
        $count = 20000;
        $key_length = 128;
    }

    $hash_length = strlen(hash($algorithm, "", true));
    $block_count = ceil($key_length / $hash_length);

    $output = "";
    for ($i = 1; $i <= $block_count; $i++) {
        $last = $salt . pack("N", $i);
        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
        for ($j = 1; $j < $count; $j++) {
            $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
        }
        $output .= $xorsum;
    }

    if ($raw_output) {
        return substr($output, 0, $key_length);
    } else {
        return base64_encode(substr($output, 0, $key_length));
    }
}
?>