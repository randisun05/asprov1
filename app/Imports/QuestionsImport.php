<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $question_category_id;

    public function __construct($question_category_id)
    {
        $this->question_category_id = $question_category_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Question([
            'question_category_id'  => (int) $this->question_category_id,
            'text'  => $row['text'],
            'a'  => $row['a'],
            'b'  => $row['b'],
            'c'  => $row['c'],
            'd'  => $row['d'],
            'e'  => $row['e'],
            'answer'    => $row['answer'],
        ]);
    }
}
