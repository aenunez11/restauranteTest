<?php

namespace Domain\Services\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandService
{
    const OUTPUT_TOTAL_TEXT = "total";
    const NAME_VALUE_DISTANCE_NUMBER = 25;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var int
     */
    private $timer;

    /**
     * @var ProgressBar
     */
    private $progressBar;


    /* ****************************************** GETTERS & SETTERS ************************************** */

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return ProgressBar
     */
    public function getProgressBar()
    {
        if (is_null($this->progressBar)) {
            $this->progressBar = new ProgressBar($this->getOutput());
        }
        return $this->progressBar;
    }

    /**
     * @param ProgressBar $progressBar
     */
    public function setProgressBar($progressBar)
    {
        $this->progressBar = $progressBar;
    }


    /* ****************************************** PRINTING METHODS *************************************** */

    /**
     * Merge all success results of printExecutionResult method
     *
     * @param $arrayResults
     * @return array
     */
    public function mergeCalculationSingleResults($arrayResults)
    {
        $arrayValues = array();
        $matches = null;
        /* @var $successResult \stdClass */
        foreach ($arrayResults['success'] as $successResult) {
            $output = $successResult->output;
            preg_match_all('/ - (.*):\s+(\d+)/im', $output, $matches);
            // We have content
            if (count($matches) > 2) {
                $individualResults = array_combine(array_values($matches[1]), array_values($matches[2]));
                foreach ($individualResults as $nameKey => $value) {
                    $name = lcfirst($nameKey);
                    if (!array_key_exists($name, $arrayValues)) {
                        $arrayValues[$name] = $value;
                    } else {
                        $arrayValues[$name] += $value;
                    }
                }
            }
        }
        return $arrayValues;
    }

    /**
     * Print results of Calculating any process ($resultArray with KEYS and VALUES]
     *
     * @param $resultArray array
     */
    public function printExecutionResult($resultArray)
    {
        $elapsedTime = $this->calculateTimeElapsed();
        $this->output->writeln("\n");
        $this->output->writeln("<options=bold>******************* DONE! Results of the process *******************</>\n");
        $totalValue = $resultArray[self::OUTPUT_TOTAL_TEXT];
        unset($resultArray[self::OUTPUT_TOTAL_TEXT]);

        // Printing new line
        $this->output->writeln('');

        // Printing all values
        foreach ($resultArray as $nameKey => $value) {
            $name = ucfirst($nameKey);
            $percentile = $this->getPercentile($value, $totalValue);
            $blankSpaces = $this->createBlankSpacesFromCounter(self::NAME_VALUE_DISTANCE_NUMBER, strlen($name));
            $this->output->writeln(
                " <fg=green> - $name: $blankSpaces </><fg=green;options=bold>$value ($percentile %)</>"
            );
        }
        // Printing TOTAL values
        $totalName = ucfirst(self::OUTPUT_TOTAL_TEXT);
        $blankSpaces = $this->createBlankSpacesFromCounter(self::NAME_VALUE_DISTANCE_NUMBER, strlen($totalName));
        $this->output->writeln(
            " <fg=green> - $totalName: $blankSpaces </><fg=green;options=bold>$totalValue</>"
        );

        // Elapsed time
        $this->output->writeln("\n   ---- <comment>Elapsed time:          $elapsedTime</comment> ----");
        $this->output->writeln('');
    }

    /**
     * Print Commands with errors and commands to repeat in Parallel execution
     *
     * @param $parallelResultArray
     */
    public function printErrorResults($parallelResultArray)
    {
        $errorsArray = $parallelResultArray['errors'];
        echo "\n \n ****************** ERRORS DETECTED ****************** \n";
        foreach ($errorsArray as $page => $error) {
            echo "\n -------- PAGE $page -------- \n";
            echo " - Command: " . $error->command . " \n";
            echo " - Error: " . $error->output . " \n";
        }
        // Print all commands easy to execute
        if (count($errorsArray) > 0) {
            echo "\n \n ****************** COMMANDS TO EXECUTE AGAIN ****************** \n";
            foreach ($errorsArray as $error) {
                echo $error->command . " \n";
            }
        }
    }

    /* ***************************************** AUXILIARY METHODS *************************************** */

    /**
     * Return [number] blank spaces calculating by max - min
     *
     * @param $max
     * @param $min
     * @return string
     */
    private function createBlankSpacesFromCounter($max, $min)
    {
        $blankSpaces = '';
        $blankSpacesCounter = $max - $min;
        for ($i = 0; $i < $blankSpacesCounter; $i++) {
            $blankSpaces .= ' ';
        }
        return $blankSpaces;
    }

    /**
     * Calculate percentile from stat number over total
     * @param $value
     * @param $total
     * @return float|int
     */
    public function getPercentile($value, $total)
    {
        return ($total == 0) ? 0 : round(($value / $total) * 100, 2);
    }


    /**
     * Initialize timer variable
     */
    public function initializeTimer()
    {
        $this->timer = microtime(true);
    }

    /**
     * Calculate elapsed time
     */
    public function calculateTimeElapsed()
    {
        $elapsed = microtime(true) - $this->timer;
        return gmdate("H:i:s", $elapsed);
    }
}
