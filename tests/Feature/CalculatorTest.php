<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Console\Commands\CalculatorCommand;

class CalculatorTest extends TestCase
{

	//测试 加法
    public function test_basic_calculation() {
    	$input = '1+1';
    	$result = 2;
    	$this->evaluate($input, $result);
    }
    //测试除法
    public function test_calculation_division() {
    	$input = '2/2';
    	$result = 1;
    	$this->evaluate($input, $result);

    }
    //测试优先顺序
    public function test_operator_priority() {
    	$input = '1+2*3';
    	$result = 7;
    	$this->evaluate($input, $result);

    }
    //测试带括号优先顺序
    public function test_operator_priority_parentheses() {
    	$input = '(1+2)*3';
    	$result = 9;
    	$this->evaluate($input, $result);

    }

    public function evaluate($input, $result) {
    	$command = new CalculatorCommand;
    	$commandResult = $command->evaluate($input);
    	$this->assertEquals($result, $commandResult);
    } 

}

