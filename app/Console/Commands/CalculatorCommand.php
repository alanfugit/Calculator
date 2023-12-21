<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform calculations based on user input';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        while(true) {
            //获得输入
            $input = $this->ask('Please enter an expression');

            // 移除所有空格
            $expression = str_replace(' ', '', $input);

            try {
                $result = $this->evaluate($expression);
                $this->line($result);
            } catch (\Throwable $e) {
                $this->error('Invalid expression');
            }
        }
    }

    // 使用eval函数计算表达式的值
    public function evaluate($input)
    {
        return eval("return {$input};");
    }

}
