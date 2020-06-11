<?php

namespace Domain\Services\Command;

use Symfony\Component\Process\Process;

class MultiprocessService
{
    // Timeout up to 8 hours
    const PROCESS_TIMEOUT_PARALLEL_LAUNCH = 48000;

    /**
     * @var array
     */
    private $processQueue;
    /**
     * @var Bool
     */
    private $poolBusy;
    /**
     * @var array
     */
    private $processResultArray;


    /* ***************************************** METHODS ***************************************** */

    /**
     * DataHelperMergeService constructor.
     */
    public function __construct()
    {
        $this->initProcessResultArray();
    }

    /**
     * Returns ProcessResultArray and re-init the array
     *
     * @return array
     */
    public function getProcessResultArray()
    {
        $resultArray = $this->processResultArray;
        $this->initProcessResultArray();
        return $resultArray;
    }

    /**
     * Init process result array with content
     */
    public function initProcessResultArray()
    {
        $this->processResultArray = array(
            'success' => array(),
            'errors' => array()
        );
    }

    /**
     * Execute pool of processes paginated by CITIES
     *
     * @param $commandRoot
     * @param $commandOption
     * @param $numberOfProcesses
     * @param $multivaluesMatrix
     * @param $justPrintCommands bool
     * @param $optionalCommands string
     * @return array
     */
    public function poolPaginatedByArrayMultivalues($commandRoot, $commandOption, $numberOfProcesses, $multivaluesMatrix, $justPrintCommands = true, $optionalCommands = null)
    {
        echo "\n - EXECUTING IN PARALLEL $numberOfProcesses PROCESSES IN PARALLEL WITH MULTIPLE VALUES (cities, etc)... \n";
        $commandsResult = array();

        /* STEP 1: Create whole array of commands */
        foreach ($multivaluesMatrix as $processKey => $valuesArray) {
            $fullCommand = $commandRoot;
            foreach ($valuesArray as $value) {
                $fullCommand .= ' ' . $commandOption . '="' . $value . '"';
            }
            if (!is_null($optionalCommands)) {
                $fullCommand .= ' ' . $optionalCommands;
            }
            $commandsResult[] = $fullCommand;
        }
        /* STEP 2: Run pool of commands dividing the commandArrays in $numberOfProcesses */
        $commandByProcesses = array_chunk($commandsResult, $numberOfProcesses);
        foreach ($commandByProcesses as $page => $commandList) {
            if (!$justPrintCommands) {
                echo "\n \n ************************************ PAGE " . ($page + 1) . " / " . count($commandByProcesses) . " ************************************ \n";
                $this->pool($commandList, self::PROCESS_TIMEOUT_PARALLEL_LAUNCH);
                $this->wait($page * count($commandList));
            }
        }
        return $commandsResult;
    }

    /**
     * Execute pool of processes paginated bi ID RANGE
     *
     * @param $command
     * @param $numberOfProcesses
     * @param $datasetSize
     * @param $idsArray array
     * @param $justPrintCommands
     * @return array
     */
    public function poolPaginatedByArrayIds($command, $numberOfProcesses, $datasetSize, $idsArray, $justPrintCommands = false)
    {
        $allCommands = array();
        $incrementLevel = ($datasetSize * $numberOfProcesses);
        $lastPage = round(ceil(count($idsArray) / $incrementLevel), 0);
        echo "\n - EXECUTING IN PARALLEL $lastPage PAGES OF $numberOfProcesses COMMANDS WITH $datasetSize ENTITIES EACH PAGE... \n";
        for ($page = 0; $page < $lastPage; $page++) {
            echo "\n \n ************************************ PAGE $page / $lastPage ************************************ \n";
            $commandOffset = $page * $incrementLevel;
            $indexOffset = $page * $numberOfProcesses;
            $commandsArray = $this->generateDistributeCommandsFromArrayIds($numberOfProcesses, $command, $commandOffset, $datasetSize, $idsArray, $indexOffset);
            if (!$justPrintCommands) {
                $this->pool($commandsArray, self::PROCESS_TIMEOUT_PARALLEL_LAUNCH);
                $this->wait();
            } else {
                $allCommands[] = $commandsArray;
            }
        }
        return $allCommands;
    }

    /**
     * Execute pool of processes paginated bi ID RANGE
     *
     * @param $command
     * @param $numberOfProcesses
     * @param $datasetSize
     * @param $valuesArray array
     * @param $justPrintCommands
     * @return array
     */
    public function poolBySingleValue($command, $numberOfProcesses, $valuesArray, $justPrintCommands = false)
    {
        $allCommands = array();
        $processedIndex = 0;
        $lastPage = round(ceil(count($valuesArray) / $numberOfProcesses), 0);
        echo "\n - EXECUTING IN PARALLEL $lastPage PAGES OF $numberOfProcesses COMMANDS WITH $numberOfProcesses ENTITIES EACH PAGE... \n";
        for ($page = 0; $page < $lastPage; $page++) {
            echo "\n \n ************************************ PAGE ".($page+1). " / $lastPage ************************************ \n";
            $pageCommandsArray = [];
            for ($numValue = $processedIndex; $numValue < $numberOfProcesses * ($page + 1); $numValue++) {
                if ($numValue >= count($valuesArray)) {
                    break;
                }
                $pageCommandsArray[] = sprintf($command, $valuesArray[$numValue]);
                $processedIndex++;
            }
            if (!$justPrintCommands) {
                $this->pool($pageCommandsArray, self::PROCESS_TIMEOUT_PARALLEL_LAUNCH);
                $this->wait($processedIndex);
            } else {
                $allCommands[] = $pageCommandsArray;
            }
        }
        return $allCommands;
    }

    /**
     * Pooling commands in parallel
     *
     * @param $commandsArray
     * @param $timeout
     * @return bool
     */
    public function pool($commandsArray, $timeout)
    {
        /* CASE 1: If we have processes running, we cannot add more processes */
        if ($this->poolBusy) {
            echo "\n ******** Multiprocess service is busy with other processes ******** \n";
            return false;
        } /* CASE 2: If we DO NOT have processes running, start the POOL of processes */
        else {
            $this->processQueue = array();
            $maxCommands = count($commandsArray) + key($commandsArray);
            // Launching all commands in parallel
            foreach ($commandsArray as $index => $command) {
                $this->processQueue[$index] = new Process($command);
                $this->processQueue[$index]->setTimeout($timeout);
                $this->processQueue[$index]->start();
                $pid = $this->processQueue[$index]->getPid();
                echo "\n - Process " . ($index + 1) . "/$maxCommands launched (pid: $pid) [$command]";
            }
            $this->poolBusy = true;
            return true;
        }
    }

    /**
     * Capture the results when the commands are finished
     * @param $offset
     * @return bool | array
     */
    public function wait($offset = 0)
    {
        /* CASE 1: If we DO NOT have processes running, we cannot wait for anything */
        if (!$this->poolBusy) {
            echo "\n ******** Multiprocess service has not any POOL running ******** \n";
            return false;
        } /* CASE 2: If we pool of processes running, wait for the result */
        else {
            /* @var $process Process */
            foreach ($this->processQueue as $index => $process) {
                // STEP 1: Wait for the result
                $process->wait();

                // STEP 2: Process ERROR output
                if (strpos(strtolower($process->getExitCodeText()), 'error') !== false) {
                    $this->processResultArray['errors'][$index + $offset] = new \stdClass();
                    $this->processResultArray['errors'][$index + $offset]->output = $process->getErrorOutput();
                    $this->processResultArray['errors'][$index + $offset]->command = $process->getCommandLine();
                    echo "\n ******* Process $index ERRORS detected [" . $process->getCommandLine() . "] *******          \n";
                } // STEP 3: Process SUCCESS output
                else {
                    $this->processResultArray['success'][$index + $offset] = new \stdClass();
                    $this->processResultArray['success'][$index + $offset]->output = $process->getOutput();
                    $this->processResultArray['success'][$index + $offset]->command = $process->getCommandLine();
                    echo "\n ######## Process " . ($index + 1) . " finished SUCCESSFULLY!! [" . $process->getCommandLine() . "] ########          \n";
                }
            }
            // Set poolBusy to FALSE
            $this->poolBusy = false;
            return true;
        }
    }

    /**
     * Generate an array with all values and its counters distributed in $numOfGroups groups
     * (useful when you want to allocate processes and distribute by weight)
     *
     * @param $arrayValues
     * @param $nameField
     * @param $valueField
     * @param $numOfGroups
     * @return array
     */
    public function generateArrayWithBalancedValues($arrayValues, $nameField, $valueField, $numOfGroups)
    {
        /* STEP 1: Change key names of the array with nameField & valueField */
        $valuesArrayOrdered = array();
        foreach ($arrayValues as $valueObject) {
            if ((!array_key_exists($nameField, $valueObject)) || (!array_key_exists($valueField, $valueObject))) {
                throw new \InvalidArgumentException("Array values has not right structure array['$nameField' => array, '$valueField' => array']");
            }
            $valuesArrayOrdered[] = array(
                'name' => $valueObject[$nameField],
                'weight' => $valueObject[$valueField]
            );
        }
        /* STEP 2: Initialize Balanced Array with empty values */
        $allValuesCounter = array_sum(array_column($valuesArrayOrdered, 'weight'));
        $valuesListBalanced = array();
        for ($i = 0; $i < $numOfGroups; $i++) {
            $valuesListBalanced[$i] = array(
                'weight' => intval(ceil($allValuesCounter / $numOfGroups)),
                'available' => true,
                'values' => array()
            );
        }
        /* STEP 3: Refill $valuesListBalanced array with Balanced number of arrays */
        $index = 0;
        $counter = 0;
        while (!empty($valuesArrayOrdered)) {
            // Base case, forced exit
            if ($counter > $allValuesCounter) {
                break;
            }
            $isStored = false;
            $valueObject = array_shift($valuesArrayOrdered);
            // Check if we have space to store it
            if ($valuesListBalanced[$index]['available']) {
                $valuesListBalanced[$index]['weight'] -= $valueObject['weight'];
                $valuesListBalanced[$index]['values'][] = $valueObject['name'];
                $valuesListBalanced[$index]['available'] = ($valuesListBalanced[$index]['weight'] > 0);
                $isStored = true;
            }
            // If we cannot move the entity, reinsert again
            if (!$isStored) {
                array_unshift($valuesArrayOrdered, $valueObject);
            }
            $index = ($index + 1) % $numOfGroups;
            $counter++;
        }
        /* STEP 4: Refill last values if the $valuesArrayOrdered is not empty */
        foreach ($valuesArrayOrdered as $key => $value) {
            $index = $key / $numOfGroups;
            $valuesListBalanced[$index]['values'][] = $value['name'];
        }
        $valuesListBalancedNotEmpty = array();
        /* STEP 5: Only keep not empty groups */
        foreach ($valuesListBalanced as $value) {
            if (!empty($value["values"])) {
                $valuesListBalancedNotEmpty[] = $value;
            }
        }
        return $valuesListBalancedNotEmpty;
    }

    /**
     * Generate array of commands with increments and offsets
     *
     * @param $numProcesses
     * @param $command
     * @param $offset
     * @param $increment
     * @param $arrayIds
     * @param $commandIndexSuffix
     * @return array
     */
    public function generateDistributeCommandsFromArrayIds($numProcesses, $command, $offset, $increment, $arrayIds, $commandIndexSuffix = 0)
    {
        $final = false;
        $commandsArray = array();
        $arrayKeys = array_keys($arrayIds);
        $finalKey = end($arrayKeys);
        for ($i = 0; $i < $numProcesses; $i++) {
            // Initial index
            $iniIndex = $offset + ($i * $increment);
            $idIni = $arrayIds[$iniIndex];

            // End index, compare if finalId is smaller than the calculated (if the array has not enough entities)
            $endIndex = $iniIndex + $increment;
            if ($finalKey < $endIndex) {
                $idEnd = $arrayIds[$finalKey] + 1;
                $final = true;
            } else {
                $idEnd = $arrayIds[$endIndex];
            }
            $commandWithIncrement = sprintf($command, $idIni, $idEnd);
            $index = intval($i + $commandIndexSuffix);
            $commandsArray[$index] = $commandWithIncrement;
            if ($final) {
                break;
            }
        }
        return $commandsArray;
    }

}