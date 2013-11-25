<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2013 César Rodas                                                  |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/

namespace crodas\InfluxPHP;

class Client extends BaseHTTP
{
    protected $host;
    protected $port;
    protected $user;
    protected $pass;

    public function __construct($host = "localhost", $port = 8086, $u = 'root', $p = 'root')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $u;
        $this->pass = $p;
    }

    public function deleteDatabase($name)
    {
        return $this->delete("db/$name");
    }

    public function createDatabase($name)
    {
        $this->post('db', array('name' => $name));
        return new DB($this, $name);
    }

    public function getDatabases()
    {
        $self = $this;
        return array_map(function($obj) use($self) {
            return new DB($self, $obj['name']);
        }, $this->get('dbs'));
    }

    /**
     * Check if a database exists
     * 
     * @return boolean
     */
    public function databaseExists($dbname) {
        $dbs = $this->getDatabases();
        $found = false;
        foreach ($dbs as $db) {
            if ($db->getName() == $dbname) {
                $found = true;
            }
        }
        
        return $found;
    }
    
    public function getDatabase($name)
    {
        return new DB($this, $name);
    }

    public function __get($name)
    {
        return new DB($this, $name);
    }
}

