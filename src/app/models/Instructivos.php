<?php

class Instructivos
{
    public const DOCUMENTOS = [
        'member_portal' => [
            'titulo' => 'PAN MEMBER PORTAL GUIA PARA ASEGURADOS',
            'archivo' => 'PAN MEMBER PORTAL GUIA PARA ASEGURADOS_Rev4.pdf',
            'publico' => true,
        ],
        'car_rental_clase1' => [
            'titulo' => 'PANAMA CAR RENTAL - Clase 1 Gerentes',
            'archivo' => 'PANAMA CAR RENTAL, S.A. 44528 Clase 1 – Gerentes.pdf',
            'publico' => false,
        ],
        'car_rental_clase2' => [
            'titulo' => 'PANAMA CAR RENTAL - Clase 2 Administrativos',
            'archivo' => 'PANAMA CAR RENTAL, S.A. 44528 Clase 2 – Administrativos.pdf',
            'publico' => false,
        ],
        'car_rental_clase3' => [
            'titulo' => 'PANAMA CAR RENTAL - Clase 3 Resto del Personal',
            'archivo' => 'PANAMA CAR RENTAL, S.A. 44528 Clase 3 – Resto del Personal.pdf',
            'publico' => false,
        ],
    ];

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function urlPdf(string $archivo): string
    {
        return BASE_URL_IMAGE . 'instructivos/' . rawurlencode($archivo);
    }

    public function codigoValido(string $codigo): bool
    {
        return isset(self::DOCUMENTOS[$codigo]);
    }

    public function puedeVer(string $documentoCodigo, string $codigoEmpleado, bool $esAdminRrhh): bool
    {
        if (!$this->codigoValido($documentoCodigo)) {
            return false;
        }

        $doc = self::DOCUMENTOS[$documentoCodigo];
        if (!empty($doc['publico'])) {
            return true;
        }

        if ($esAdminRrhh) {
            return true;
        }

        return $this->estaAsignado($documentoCodigo, $codigoEmpleado);
    }

    public function estaAsignado(string $documentoCodigo, string $codigoEmpleado): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM instructivos_asignacion
             WHERE documento_codigo = :doc AND codigo_empleado = :codigo LIMIT 1'
        );
        $stmt->execute([
            ':doc' => $documentoCodigo,
            ':codigo' => $this->normalizarCodigo($codigoEmpleado),
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function listarAsignados(string $documentoCodigo): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ia.id, ia.codigo_empleado, ia.asignado_por, ia.fecha_asignacion,
                    e.nombre, e.apellido
             FROM instructivos_asignacion ia
             LEFT JOIN empleados e ON e.codigo_empleado = ia.codigo_empleado
             WHERE ia.documento_codigo = :doc
             ORDER BY e.nombre ASC, e.apellido ASC'
        );
        $stmt->execute([':doc' => $documentoCodigo]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignar(string $documentoCodigo, string $codigoEmpleado, string $asignadoPor): bool
    {
        if (!$this->codigoValido($documentoCodigo) || !empty(self::DOCUMENTOS[$documentoCodigo]['publico'])) {
            return false;
        }

        $codigo = $this->normalizarCodigo($codigoEmpleado);
        if ($codigo === '') {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO instructivos_asignacion (documento_codigo, codigo_empleado, asignado_por)
             VALUES (:doc, :codigo, :asignado_por)
             ON DUPLICATE KEY UPDATE asignado_por = VALUES(asignado_por), fecha_asignacion = CURRENT_TIMESTAMP'
        );

        return $stmt->execute([
            ':doc' => $documentoCodigo,
            ':codigo' => $codigo,
            ':asignado_por' => $this->normalizarCodigo($asignadoPor),
        ]);
    }

    public function quitar(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM instructivos_asignacion WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function listarColaboradoresActivos(): array
    {
        $stmt = $this->pdo->query(
            'SELECT e.codigo_empleado, e.nombre, e.apellido
             FROM empleados e
             INNER JOIN empleado_log el ON e.codigo_empleado = el.codigo
             WHERE el.stat = 1
             ORDER BY e.nombre ASC, e.apellido ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarColaboradores(string $termino, int $limite = 20): array
    {
        $termino = trim($termino);
        if (strlen($termino) < 2) {
            return [];
        }

        $like = '%' . $termino . '%';
        $stmt = $this->pdo->prepare(
            'SELECT e.codigo_empleado, e.nombre, e.apellido
             FROM empleados e
             INNER JOIN empleado_log el ON e.codigo_empleado = el.codigo
             WHERE el.stat = 1
               AND (e.codigo_empleado LIKE :t OR e.nombre LIKE :t OR e.apellido LIKE :t
                    OR CONCAT(e.nombre, " ", e.apellido) LIKE :t)
             ORDER BY e.nombre ASC, e.apellido ASC
             LIMIT :limite'
        );
        $stmt->bindValue(':t', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function normalizarCodigo(string $codigo): string
    {
        return trim($codigo);
    }
}
