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

    /**
     * 执行函数
     * @param String $input 用户输入字符串
     * @return Int
     */
    public function evaluate($input): Int
    {
        if(strpos($input, '(') !== false) { //检查输入是否包含()括号，包含就取出计算，不包含直接运算
            //查找最内层的()里面的数据，并运算后替换掉输入字符串
            $blockFirst = strrpos($input, '('); //从右开始，取得最后的（ 的键      
            $blockEnd = strpos($input, ')', $blockFirst); //根据开始位置匹配最后的关闭标签
            $operAllStr = substr($input, $blockFirst, $blockEnd - $blockFirst + 1);//截取最里面括号字符串
            
            $operStr = str_replace(['(', ')'], '', $operAllStr); //过滤括号取得运算字符串
            $result = $this->operationStr($operStr); //当前计算结果

            $repStr = str_replace($operAllStr, $result, $input); //把结果替换掉优先级括号

            return $this->evaluate($repStr);
            
        }else {

            return $this->operationStr($input);
        }
        //return eval("return {$input};");
    }

    /**
     * 执行运算表达式字符串
     * @param String $operStr
     * @return Int
     */
    public function operationStr($operStr): Int 
    {
        $result = 0; //结果初始化

        preg_match_all('/(\\d+)|([-+()*\/])/', $operStr, $matches);// 用正则解析表达式把数据解出来
        
        $operArr = $matches[0];

        $result = $this->operation($operArr, true); //执行乘法除法
        
        if(count($operArr) > 1) { //如果执行乘和除法还没算完，才继续执行加法运算, 如果执行加法，以+法算完的结果为准
            $addAndSubOperArr = array_values($operArr); //把操作数组重新生成Key值顺序，剩下的就只是+-法了
            $result = $this->operation($addAndSubOperArr);
        }
        return $result;
    }

    /**
     * 运算结果
     * @param Array &$array 引用数组，正则匹配后的匹配数组
     * @param Boolean $priority 控制是否先执行乘法和除法高优先级运算
     * @return Int
     */
    public function operation(&$array, $priority=false): Int
    {
        $arrCount = count($array);
        $result = 0; //结果初始化
        for($i = 1; $i < $arrCount; $i+=2) { //表达式运算符都是左右两边都有数字的，取得运算符运算两边的数值计算结果就可以
             
             if($priority && in_array($array[$i], ['+', '-'])) { //如果要计算优先级的乘法和除法，跳过本次循环
                continue;
             }
             //取得运算符两边的数字
             $adjacentArr = $this->getAdjacentKeys($array, $i); 
             $leftNum = $adjacentArr[0]; //取得运算符左边数值
             $rightNum = $adjacentArr[1]; //取得运算符右边数值
             $result = $this->execOper($leftNum, $rightNum, $array[$i]); //计算结果

             //把运算后的数据清除
             unset($array[$i-1], $array[$i], $array[$i+1]);
             //把结果放进去清除的位置，并不改变原来的键值对,因为添加进去虽然指定了键值，但是排序还是在最后，所以要排序一下
             $array[$i] = $result;
             ksort($array);
        }

        return $result;
    }

    /**
     * 执行运算符实际运算
     * @param Int $num1 计算的第一个数值
     * @param Int $num2 计算的第二个数值
     * @param String $char 运算符号
     * @return Int
     */
    public function execOper($num1, $num2, $char): Int
    {
        $res = 0;
        switch($char) {
            case '+':
                $res = $num1 + $num2;
                break;
            case '-': 
                $res = $num1 - $num2;
                break; 
            case '*': 
                $res = $num1 * $num2;
                break; 
            case '/': 
                $res = $num1 / $num2;
                break; 
            default:
                break;
        }
        return $res;
    }

    /**
     * 取得数组给定键的两边的值
     * @param Array $array 要操作的数组
     * @param Int $key 数组的某个键值
     * @return Array 反正前面和后面的数值
     */
    public function getAdjacentKeys($array, $key): Array 
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        return [
            $array[$keys[$index - 1]] ?? null, 
            $array[$keys[$index + 1]] ?? null
        ];
    }

}
