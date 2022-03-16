<?php

function get_weak_auth_password(): string
{
    return config('pass');
}

function get_weak_auth_hashed_password(): string
{
    return password_hash(config('pass'), PASSWORD_DEFAULT);
}

function password_verify_weak_auth($hash): bool
{
    return password_verify(config('pass'), $hash);
}

function _p_field(): void
{
    $hash = get_weak_auth_hashed_password();
    echo "<input type='hidden' name='_p' value='$hash'>";
}
