<?php

/*
 * The MIT License
 *
 * Copyright 2014 Juan Manuel Agüero <jaguero@flowcode.com.ar>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace flowcode\ceibo\support;

use flowcode\ceibo\data\DataSource;

/**
 * Description of ScriptRunner
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class ScriptRunner {

    private $dataSource;

    function __construct($dataSource = null) {
        if (!is_null($dataSource)) {
            $this->dataSource = $dataSource;
        }
    }

    public function run($dbms_schema) {
        $success = false;
        $sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) or die('problem ');
        $sql_query = remove_remarks($sql_query);
        $sql_query = split_sql_file($sql_query, ';');
        
        $i = 1;
        foreach ($sql_query as $sql) {
            $this->getDataSource()->query($sql) or die("error in query $i");
//            mysql_query($sql) or die("error in query $i");
            $i++;
        }
        $success = true;
        return $success;
    }

    function remove_comments(&$output) {
        $lines = explode("\n", $output);
        $output = "";

        // try to keep mem. use down
        $linecount = count($lines);

        $in_comment = false;
        for ($i = 0; $i < $linecount; $i++) {
            if (preg_match("/^\/\*/", preg_quote($lines[$i]))) {
                $in_comment = true;
            }

            if (!$in_comment) {
                $output .= $lines[$i] . "\n";
            }

            if (preg_match("/\*\/$/", preg_quote($lines[$i]))) {
                $in_comment = false;
            }
        }

        unset($lines);
        return $output;
    }

    /**
     * Remove_remarks will strip the sql comment lines out of an uploaded sql file.
     * @param string $sql
     * @return string
     */
    function remove_remarks($sql) {
        $lines = explode("\n", $sql);

        // try to keep mem. use down
        $sql = "";

        $linecount = count($lines);
        $output = "";

        for ($i = 0; $i < $linecount; $i++) {
            if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) {
                if (isset($lines[$i][0]) && $lines[$i][0] != "#") {
                    $output .= $lines[$i] . "\n";
                } else {
                    $output .= "\n";
                }
                // Trading a bit of speed for lower mem. use here.
                $lines[$i] = "";
            }
        }

        return $output;
    }

    /**
     * Split_sql_file will split an uploaded sql file into single sql statements.
     * Note: expects trim() to have already been run on $sql.
     * @param string $sql
     * @param type $delimiter
     * @return string
     */
    function split_sql_file($sql, $delimiter) {
        // Split up our string into "possible" SQL statements.
        $tokens = explode($delimiter, $sql);

        // try to save mem.
        $sql = "";
        $output = array();

        // we don't actually care about the matches preg gives us.
        $matches = array();

        // this is faster than calling count($oktens) every time thru the loop.
        $token_count = count($tokens);
        for ($i = 0; $i < $token_count; $i++) {
            // Don't wanna add an empty string as the last thing in the array.
            if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
                // This is the total number of single quotes in the token.
                $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
                // Counts single quotes that are preceded by an odd number of backslashes,
                // which means they're escaped quotes.
                $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

                $unescaped_quotes = $total_quotes - $escaped_quotes;

                // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
                if (($unescaped_quotes % 2) == 0) {
                    // It's a complete sql statement.
                    $output[] = $tokens[$i];
                    // save memory.
                    $tokens[$i] = "";
                } else {
                    // incomplete sql statement. keep adding tokens until we have a complete one.
                    // $temp will hold what we have so far.
                    $temp = $tokens[$i] . $delimiter;
                    // save memory..
                    $tokens[$i] = "";

                    // Do we have a complete statement yet?
                    $complete_stmt = false;

                    for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
                        // This is the total number of single quotes in the token.
                        $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                        // Counts single quotes that are preceded by an odd number of backslashes,
                        // which means they're escaped quotes.
                        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

                        $unescaped_quotes = $total_quotes - $escaped_quotes;

                        if (($unescaped_quotes % 2) == 1) {
                            // odd number of unescaped quotes. In combination with the previous incomplete
                            // statement(s), we now have a complete statement. (2 odds always make an even)
                            $output[] = $temp . $tokens[$j];

                            // save memory.
                            $tokens[$j] = "";
                            $temp = "";

                            // exit the loop.
                            $complete_stmt = true;
                            // make sure the outer loop continues at the right point.
                            $i = $j;
                        } else {
                            // even number of unescaped quotes. We still don't have a complete statement.
                            // (1 odd and 1 even always make an odd)
                            $temp .= $tokens[$j] . $delimiter;
                            // save memory.
                            $tokens[$j] = "";
                        }
                    } // for..
                } // else
            }
        }

        return $output;
    }

    /**
     * Get a DataSource instance.
     * @return DataSource dataSource.
     */
    public function getDataSource() {
        return $this->dataSource;
    }

    public function setDataSource($dataSource) {
        $this->dataSource = $dataSource;
    }

}
