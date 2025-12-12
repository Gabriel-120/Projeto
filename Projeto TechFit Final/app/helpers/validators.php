<?php

function validarCPF(string $cpf): bool
{
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    return strlen($cpf) === 11;

}

function formatTelefone(string $digits): string
{
    $d = preg_replace('/[^0-9]/', '', $digits);
    $len = strlen($d);

    if ($len === 11) {
        return '(' . substr($d, 0, 2) . ') ' . substr($d, 2, 5) . '-' . substr($d, 7, 4);
    }

    if ($len === 10) {
        return '(' . substr($d, 0, 2) . ') ' . substr($d, 2, 4) . '-' . substr($d, 6, 4);
    }

    return $digits;
}

function validarForcaSenha(string $senha): bool
{
    if (!preg_match('/[A-Z]/', $senha)) {
        return false;
    }

    if (!preg_match('/[a-z]/', $senha)) {
        return false;
    }

    if (!preg_match('/[0-9]/', $senha)) {
        return false;
    }

    return true;
}

function formatCPF(string $cpf): string
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) === 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}