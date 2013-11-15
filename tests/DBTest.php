<?php
use crodas\InfluxPHP\Client;
use crodas\InfluxPHP\DB;

class DBTest extends \phpunit_framework_testcase
{
    public function testCreate()
    {
        $client = new Client;
        return $client->createDatabase("test_foobar");
    }

    /**
     *  @expectedException RuntimeException
     */
    public function testCreateException()
    {
        $client = new Client;
        return $client->createDatabase("test_foobar");
    }

    /**
     *  @dependsOn testCreateException
     */
    public function testDelete()
    {
        $client = new Client;
        return $client->deleteDatabase("test_foobar");
    }

    /**
     *  @dependsOn testDelete
     *  @expectedException RuntimeException
     */
    public function testDeleteException()
    {
        $client = new Client;
        return $client->deleteDatabase("test_foobar");
    }

    public function testDBObject()
    {
        $client = new Client;
        $client->createDatabase("test_xxx");
        $this->assertTrue($client->test_xxx instanceof DB);
        $this->assertTrue($client->getDatabase("test_xxx") instanceof DB);
        $client->test_xxx->drop();
    }

    public function testQuery()
    {
        $client = new Client;
        $db = $client->createDatabase("test_xxx");
        $db->createUser("root", "root");

        $db->insert("foobar", array('type' => '/foobar', 'karma' => 10));
        $db->insert("foobar", array('type' => '/foobar', 'karma' => 20));
        $db->insert("foobar", array('type' => '/barfoo', 'karma' => 30));

        $this->assertEquals($db->first("SELECT max(karma) FROM foobar")->max, 30);
        $this->assertEquals($db->first("SELECT min(karma) FROM foobar")->min, 10);
        $this->assertEquals($db->first("SELECT mean(karma) FROM foobar")->mean, 20);

        foreach ($db->query("SELECT mean(karma) FROM foobar GROUP BY type") as $row) {
            if ($row->type == "/foobar") {
                $this->assertEquals(15, $row->mean);
            } else {
                $this->assertEquals(30, $row->mean);
            }
        }

        $db->drop();
    } 
}
