<?php

$i = 1;

function addNoArgument($xml, $instruction)
{
    global $i;
    $newElement=$xml->addChild('instruction');
    $newElement->addAttribute('order',$i);
    $newElement->addAttribute('opcode',$instruction);
    $i = $i+1;
    return $xml;
}

function addOneArgument($xml, $instruction, $argument, $type)
{
    global $i;
    $newElement=$xml->addChild('instruction');
    $newElement->addAttribute('order',$i);
    $newElement->addAttribute('opcode',$instruction);
    $newArgument=$newElement->addChild('arg1',$argument);
    $newArgument->addAttribute('type',$type);
    $i = $i+1;
    return $xml;
}


function addTWOArgument($xml, $instruction, $argument, $type, $argument2, $type2)
{
    global $i;
    $newElement=$xml->addChild('instruction');
    $newElement->addAttribute('order',$i);
    $newElement->addAttribute('opcode',$instruction);
    $newArgument=$newElement->addChild('arg1',$argument);
    $newArgument->addAttribute('type',$type);
    $newArgument=$newElement->addChild('arg2',$argument2);
    $newArgument->addAttribute('type',$type2);
    $i = $i+1;
    return $xml;
}


function addThreeArgument($xml, $instruction, $argument, $type, $argument2, $type2, $argument3, $type3)
{
    global $i;
    $newElement=$xml->addChild('instruction');
    $newElement->addAttribute('order',$i);
    $newElement->addAttribute('opcode',$instruction);
    $newArgument=$newElement->addChild('arg1',$argument);
    $newArgument->addAttribute('type',$type);
    $newArgument=$newElement->addChild('arg2',$argument2);
    $newArgument->addAttribute('type',$type2);
    $newArgument=$newElement->addChild('arg3',$argument3);
    $newArgument->addAttribute('type',$type3);
    $i = $i+1;
    return $xml;
}



function isVar($word)
{
    return preg_match("/\A(LF|GF|TF)@[a-zA-Z0-9_\-$;&%*!?]*\z/i", $word);
}


function isSymbol($word)
{
    return isVar($word) || isInt($word) || isString($word) || isBool($word) || isNil($word);
}


function isInt($word)
{
    return preg_match("/\A(int)@(\+|\-|)([0-9]+|(0x[0-9A-Fa-f]+)|(0X[0-9A-Fa-f]+)|(0o[0-7]+)|(0O[0-7]+))\z/i", $word) || isVar($word);
}
function isLabel($word)
{
    return preg_match("/\A[a-zA-Z0-9_\-$;&%*!?]+\z/i", $word);
    
}

function isString($word)
{
    return preg_match("/\A(string)@([a-zA-Z0-9_\-$;&%*!?\/]|(\\\\[0-9]{3}))+\z/", $word) || isVar($word);
    
}


function isBool($word)
{
    return preg_match("/\A(bool)@(true|false)\z/", $word) || isVar($word);
}


function isNil($word)
{
    return preg_match("/\A(nil)@(nil)\z/", $word) || isVar($word);
}

function isType($word)
{
    return preg_match("/\A((int)|(bool)|(string))\z/i", $word) || isVar($word);
}


function getTypeIpp($word)
{
    if(isVar($word))
        return "var";
    elseif(isInt($word))
        return "int";
    elseif(isString($word))
        return "string";
    elseif(isBool($word))
        return "bool";
    elseif(isNil($word))
        return "nil";
    elseif(isType($word))
    return "type";
    elseif(isLabel($word))
        return "label";
    else
    {
        echo "Neznamy typ hodnoty !";
        exit(23);
    }
}


function getValue($word)
{
    if (isLabel($word))
    {
        return $word;
    }
    if (!(isVar($word)))
    {
        $word = explode('@', trim($word, "\n"));
        return $word[1];
    }
    else
        return $word;
}


function addProgHeader($xml, $words)
{
    $words[0] = strtoupper($words[0]);
    if($words[0] == ".IPPCODE22")
    {
        $xml->addAttribute('language', 'IPPcode22');
        return $xml;
    }
    else exit(21);
}


function addElement($xml, $words)
{
    if($words != NULL)
    {
    switch (strtoupper($words[0]))
    {
        case "DEFVAR":
            if (count($words) == 2)
            {
                if (isVar($words[1]))
                {
                    $xml = addOneArgument($xml, "DEFVAR", $words[1], "var");
                }
                else
                    exit(23);
            } 
            else
            {
                exit(23);
            }
            break;


        case "MOVE":
            if (count($words) == 3)
            {
                if(isVar($words[1]) && isSymbol($words[2]))
                {
                    $xml = addTwoArgument($xml, "MOVE", $words[1], "var",getValue($words[2]),getTypeIpp($words[2]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "CREATEFRAME":
            if (count($words) == 1)
            {
                $xml = addNoArgument($xml, $words[0]);
            }
            else
            {
                exit(23);
            }
            break;


        case "PUSHFRAME":
            if (count($words) == 1)
            {
                $xml = addNoArgument($xml, $words[0]);
            }
            else
            {
                exit(23);
            }
            break;


        case "POPFRAME":
            if (count($words) == 1)
            {
                $xml = addNoArgument($xml, $words[0]);
            }
            else
            {
                exit(23);
            }
            break;


        case "CALL":
            if (count($words) == 2)
            {
                if (isLabel($words[1]))
                {
                    $xml = addOneArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "RETURN":
            if (count($words) == 1)
            {
                $xml = addNoArgument($xml, $words[0]);
            }
            else
            {
                exit(23);
            }
            break;


        case "PUSHS":
            if (count($words) == 2)
            {
                if (isVar($words[1]))
                {
                    $xml = addOneArgument($xml, "PUSHS", getValue($words[1]), getTypeIpp($words[1]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "POPS":
            if (count($words) == 2)
            {
                if (isVar($words[1]))
                {
                    $xml = addOneArgument($xml, "POPS", $words[1], "var");
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "ADD":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) && isInt($words[3]))
                {
                    $xml = addThreeArgument($xml, "ADD", getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "SUB":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) && isInt($words[3]))
                {
                    $xml = addThreeArgument($xml, "SUB", getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "MUL":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) && isInt($words[3]))
                {
                    $xml = addThreeArgument($xml, "MUL", getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "IDIV":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) && isInt($words[3]))
                {
                    $xml = addThreeArgument($xml, "IDIV", getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "LT":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) || isString($words[2]) || isBool($words[2]) && isInt($words[3]) || isString($words[3]) || isBool($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "GT":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) || isString($words[2]) || isBool($words[2]) && isInt($words[3]) || isString($words[3]) || isBool($words[3]))
                {
                        $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "EQ":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isInt($words[2]) || isString($words[2]) || isBool($words[2]) && isInt($words[3]) || isString($words[3]) || isBool($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "AND":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isBool($words[2]) && isBool($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "OR":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isBool($words[2]) && isBool($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "NOT":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isBool($words[2]) && isBool($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "INT2CHAR":
            if (count($words) == 3)
            {
                if(isVar($words[1]) && isSymbol($words[2]))
                {
                    $xml = addTwoArgument($xml, $words[0], $words[1], "var",getValue($words[2]),getTypeIpp($words[2]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "STRI2INT":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isSymbol($words[2]) && isSymbol($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "READ":
            if (count($words) == 3)
            {
                if(isVar($words[1]) && isType($words[2]))
                {
                    $xml = addTwoArgument($xml, $words[0], $words[1], "var",getValue($words[2]),getTypeIpp($words[2]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "WRITE":
            if (count($words) == 2)
            {
                if (isSymbol($words[1]))
                {
                    $xml = addOneArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "CONCAT":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isSymbol($words[2]) && isSymbol($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "STRLEN":
            if (count($words) == 3)
            {
                if(isVar($words[1]) && isSymbol($words[2]))
                {
                    $xml = addTwoArgument($xml, $words[0], $words[1], "var",getValue($words[2]),getTypeIpp($words[2]));
                }
                else
                    exit(23);
            }
            else
                {
                    exit(23);
                }
            break;


        case "GETCHAR":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isSymbol($words[2]) && isSymbol($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "SETCHAR":
            if (count($words) == 4)
            {
                if (isVar($words[1]) && isSymbol($words[2]) && isSymbol($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                exit(23);
            }
            break;


        case "TYPE":
            if (count($words) == 3)
            {
                if(isVar($words[1]) && isSymbol($words[2]))
                {
                    $xml = addTwoArgument($xml, $words[0], $words[1], "var",getValue($words[2]),getTypeIpp($words[2]));
                }
                else
                    exit(23);
            }
            break;


        case "LABEL":
            if (count($words) == 2)
            {
                if (isLabel($words[1]))
                {
                    $xml = addOneArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]));
                }
                else
                    exit(23);
            }
            else
                {
                    exit(23);
                }
            break;


        case "JUMP":
            if (count($words) == 2)
            {
                if (isLabel($words[1]))
                {
                    $xml = addOneArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]));
                }
                else
                    exit(23);
            }
            else
                {
                    exit(23);
                }
            break;


        case "JUMPIFEQ":
            if (count($words) == 4)
            {
                if (isLabel($words[1]) && isSymbol($words[2]) && isSymbol($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
            }
            else
            {
                 exit(23);
            }
            break;


        case "JUMPIFNEQ":
            if (count($words) == 4)
            {
                if (isLabel($words[1]) && isSymbol($words[2]) && isSymbol($words[3]))
                {
                    $xml = addThreeArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]), getValue($words[2]), getTypeIpp($words[2]), getValue($words[3]), getTypeIpp($words[3]));
                }
                else
                    exit(23);
                }
            else
            {
                exit(23);
            }
            break;


        case "EXIT":
            if (count($words) == 2)
                {
                    if (isSymbol($words[1]))
                    {
                        $xml = addOneArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]));
                    }
                    else
                        exit(23);
                }
                else
                {
                    exit(23);
                }
                    break;
            break;

        case "DPRINT":
            if (count($words) == 2)
            {
                if (isSymbol($words[1]))
                {
                    $xml = addOneArgument($xml, $words[0], getValue($words[1]), getTypeIpp($words[1]));
                }
                else
                    exit(23);
                }
            else
            {
                exit(23);
            }
                break;

        case "BREAK":
            if (count($words) == 1)
            {
                $xml = addNoArgument($xml, $words[0]);
            }
            else
            {
                exit(23);
            }
                
            break;
            
        
        default:
            echo "Err22: funcia \"$words[0]\" nieje definovaná v IPPcode22!\n";
            exit(22);    
        
    }
    return $xml;
    }
    else
    return $xml;
}
if(sizeof($argv) ==2 && $argv[1] == "--help")
{
    echo("\n");
    echo (" -----------------------------------------------------------------------------------\n");
    echo ("|Pouzitie : 1. parse.php [moznosti] <  vstup (typ IPPcode22) , vystup bude na STDOUT|\n");
    echo ("|           2. parse.php [moznosti] <  vstup (typ IPPcode22)  > vystup (typ xml)    |\n");
    echo ("|                                                                                   |\n");
    echo ("|Moznosti : 1. --help: vypise napovedu                                              |\n");
    echo ("|         : 2. bez parametra: vykona sa preklad na xml format                       |\n");
    echo (" -----------------------------------------------------------------------------------\n");
    echo("\n");
    exit(0);
}
elseif(sizeof($argv) > 1)
{
    exit(10);
}
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><program></program>');
$done = false;
while($done != true)
{
    $line = fgets(STDIN);
    if($line != "\n")
    {
        $lineWithoutComents = explode('#', trim($line, "\n"));
        $words = explode(' ', trim($lineWithoutComents[0], "\n"));
        $words = array_filter($words);
        $xml=addProgHeader($xml, $words);
        $done = true;
    }
    
}
$line = fgets(STDIN);
$line = str_replace('&', '&amp;', $line);
$line = str_replace('<', '&lt;', $line);
$line = str_replace('>', '&gt;', $line);
$lineWithoutComents = explode('#', trim($line, "\n"));
$words = explode(' ', trim($lineWithoutComents[0], "\n"));
$words = array_filter($words);
while ($line!=NULL)
{
$xml = addElement($xml, $words);
$line = fgets(STDIN);
$line = str_replace('&', '&amp;', $line);
$line = str_replace('<', '&lt;', $line);
$line = str_replace('>', '&gt;', $line);
$lineWithoutComents = explode('#', trim($line, "\n"));
$words = explode(' ', trim($lineWithoutComents[0], "\n"));
$words = array_filter($words);
}
$xmlOut = dom_import_simplexml($xml)->ownerDocument;
$xmlOut->formatOutput = true;
echo $xmlOut->saveXML();
?> 