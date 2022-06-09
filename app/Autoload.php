<?php

namespace app;

class Autoload
{

    private $method = null;
    private $namespace;

    private $file_location;

    public function run(): ?System
    {
        if ($this->method !== null) {
            return $this->method;
        }
        spl_autoload_register($autoload_function = [
            __CLASS__, 'include_system_file'
        ], true, false);
        $this->method = $method = new System();
        spl_autoload_unregister($autoload_function);
        $method->register(true);
        return $method;
    }

    public function include_system_file($namespace): void
    {
        if (($this->namespace = $namespace) !== null) {
            $this->attach_a_file();
        }
    }

    private function attach_a_file(): void
    {
        if ($this->check_file_exists()) {
            require $this->file_location . '.php';
            $this->check_class_exists();
        }
    }

    private function check_file_exists(): bool
    {
        $this->file_location = $file_location = $this->converter_filename();
        return file_exists($file_location . '.php');
    }

    private function converter_filename(): string
    {
        return INC_ROOT . '/' . trim(str_replace(
            '\\', DIRECTORY_SEPARATOR, $this->namespace
        ), '/');
    }

    private function check_class_exists(): void
    {
        if (class_exists($this->namespace)) {
            return;
        }
        $this->error_output(500, '');
    }

    private function error_output($code, $message = ''): void
    {
        http_response_code($response_code = $code);
        die('<pre>system@error % [' . $response_code . '] ~@ ' . $message . '...</pre>');
    }

}