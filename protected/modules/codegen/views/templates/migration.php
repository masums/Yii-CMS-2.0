<? echo "<?\n"; ?>

class <?= $name ?> extends DbMigration
{
    public function up()
    {
        $this->execute("
            <?= $sql . "\n" ?>
        ");
    }


    public function down()
    {
        $this->execute("DROP TABLE `<?= $table ?>`");
    }
}