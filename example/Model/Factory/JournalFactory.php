<?php


namespace Fredy\Model\Factory;


use  Fredy\Model\Entity\Journal;

class JournalFactory extends Factory
{

    /**
     * @param $data
     * @return Journal
     */
    public function build($data)
    {
        return new Journal($data['id'], $data['title'], $data['content']);
    }
}