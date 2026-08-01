<?php

class GestorUsuario
{
    private PDO $conexion;

    public function __construct(PDO $dbh)
    {
        $this->conexion = $dbh;
    }

    public function existeEmail(string $email): bool
    {
        $query = "SELECT 1 FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conexion->prepare($query);

        $stmt->execute([":email" => $email]);
        return $stmt->fetch() !== false;
    }

    public function existeTelefono(?string $telefono): bool
    {
        $query = "SELECT 1 FROM usuarios WHERE telefono = :telefono LIMIT 1";
        $stmt = $this->conexion->prepare($query);

        $stmt->execute([":telefono" => $telefono]);
        return $stmt->fetch() !== false;
    }

    public function guardarUsuario(string $nombre, string $email, ?string $telefono = null): bool
    {
        $query = "INSERT INTO usuarios (nombre, email,telefono)
                        VALUES(:nombre, :email, :telefono)";
        $stmt = $this->conexion->prepare($query);

        return $stmt->execute([
            ":nombre" => $nombre,
            ":email" => $email,
            ":telefono" => $telefono
        ]);
    }

    public function obtenerUsuarios(): array
    {
        $query = "SELECT nombre, email, telefono, fecha_ingresado FROM usuarios 
                    ORDER BY fecha_ingresado DESC, id DESC";
        $stmt = $this->conexion->prepare($query);

        $stmt->execute();
        return $stmt->fetchAll();
    }
}
