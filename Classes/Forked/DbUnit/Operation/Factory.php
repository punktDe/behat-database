<?php
namespace PunktDe\Testing\Forked\DbUnit\Operation;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

/**
 * A class factory to easily return database operations.
 */
class Factory
{
    /**
     * Returns a null database operation
     *
     * @return Operation
     */
    public static function NONE()
    {
        return new None();
    }

    /**
     * Returns a clean insert database operation. It will remove all contents
     * from the table prior to re-inserting rows.
     *
     * @param bool $cascadeTruncates Set to true to force truncates to cascade on databases that support this.
     *
     * @return Operation
     */
    public static function CLEAN_INSERT($cascadeTruncates = false)
    {
        return new Composite([
            self::TRUNCATE($cascadeTruncates),
            self::INSERT()
        ]);
    }

    /**
     * Returns an insert database operation.
     *
     * @return Operation
     */
    public static function INSERT()
    {
        return new Insert();
    }

    /**
     * Returns a truncate database operation.
     *
     * @param bool $cascadeTruncates Set to true to force truncates to cascade on databases that support this.
     *
     * @return Operation
     */
    public static function TRUNCATE($cascadeTruncates = false)
    {
        $truncate = new Truncate();
        $truncate->setCascade($cascadeTruncates);

        return $truncate;
    }

    /**
     * Returns a delete database operation.
     *
     * @return Operation
     */
    public static function DELETE()
    {
        return new Delete();
    }

    /**
     * Returns a delete_all database operation.
     *
     * @return Operation
     */
    public static function DELETE_ALL()
    {
        return new DeleteAll();
    }

    /**
     * Returns an update database operation.
     *
     * @return Operation
     */
    public static function UPDATE()
    {
        return new Update();
    }
}
