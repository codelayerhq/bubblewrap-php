<?php

namespace Codelayer\Bubblewrap;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Bubblewrap
{
    /**
     * @var array
     */
    private $flags = [];

    /**
     * @var string
     */
    private $binary = 'bwrap';

    /**
     * @var bool
     */
    private $clearEnv = false;

    /**
     * Execute a command in the created sandbox.
     *
     * @param array $sandboxedCommand
     *
     * @return Process
     */
    public function exec($sandboxedCommand)
    {
        $command = $this->getCommand($sandboxedCommand);

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }

    /**
     * Get the command that is used to run the sandbox.
     *
     * @param array $sandboxedCommand
     *
     * @return array
     */
    public function getCommand($sandboxedCommand)
    {
        $command = $this->flags;

        array_unshift($command, $this->binary);

        if ($this->clearEnv) {
            $command = array_merge(['env', '-i'], $command);
        }
        
        return array_merge($command, $sandboxedCommand);
    }

    /**
     * Remove all env variables by prepending `env -i` to the bwrap call.
     *
     * @param bool $clear
     *
     * @return $this
     */
    public function clearEnv($clear = true)
    {
        $this->clearEnv = $clear;

        return $this;
    }

    /**
     * Set bwrap binary to use.
     *
     * @param $binary
     *
     * @return $this
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * Add a read only bind mount.
     *
     * @param string      $source
     * @param null|string $dest
     *
     * @return $this
     */
    public function readOnlyBind($source, $dest = null)
    {
        if ($dest === null) {
            $dest = $source;
        }

        return $this->push(['--ro-bind', $source, $dest]);
    }

    /**
     * Add a read / write bind mount.
     *
     * @param string      $source
     * @param null|string $dest
     *
     * @return $this
     */
    public function bind($source, $dest = null)
    {
        if ($dest === null) {
            $dest = $source;
        }

        return $this->push(['--bind', $source, $dest]);
    }

    /**
     * Add a symlink.
     *
     * @param string $source
     * @param string $dest
     *
     * @return $this
     */
    public function symlink($source, $dest)
    {
        return $this->push(['--symlink', $source, $dest]);
    }

    /**
     * Mount procfs on dest.
     *
     * @param string $dest
     *
     * @return $this
     */
    public function proc($dest = '/proc')
    {
        return $this->push(['--proc', $dest]);
    }

    /**
     * Mount new devtmpfs on dest.
     *
     * @param string $dest
     *
     * @return $this
     */
    public function dev($dest = '/dev')
    {
        return $this->push(['--dev', $dest]);
    }

    /**
     * Mount new tmpfs on dest.
     *
     * @param string $dest
     *
     * @return $this
     */
    public function tmpfs($dest)
    {
        return $this->push(['--tmpfs', $dest]);
    }

    /**
     * Create a directory at dest.
     *
     * @param string $dest
     *
     * @return $this
     */
    public function dir($dest)
    {
        return $this->push(['--dir', $dest]);
    }

    /**
     * Create a new terminal session for the sandbox.
     *
     * @return $this
     */
    public function newSession()
    {
        return $this->push('--new-session');
    }

    /**
     * Die with the parent process.
     *
     * @return $this
     */
    public function dieWithParent()
    {
        return $this->push('--die-with-parent');
    }

    /**
     * Use a custom hostname in the sandbox (requires --unshare-uts).
     *
     * @param string $name
     *
     * @return $this
     */
    public function hostname($name)
    {
        return $this->push(['--hostname', $name]);
    }

    /**
     * Change directory to dir.
     *
     * @param string $dir
     *
     * @return $this
     */
    public function chdir($dir)
    {
        return $this->push(['--chdir', $dir]);
    }

    /**
     * Unset var.
     *
     * @param string $name
     *
     * @return $this
     */
    public function unsetenv($name)
    {
        return $this->push(['--unsetenv', $name]);
    }

    /**
     * Set env variable with name to value.
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setvar($name, $value)
    {
        return $this->push(['--setenv', $name, $value]);
    }

    /**
     * Create a new user namespace.
     *
     * @return $this
     */
    public function unshareUser()
    {
        return $this->push('--unshare-user');
    }

    /**
     * Create a new ipc namespace.
     *
     * @return $this
     */
    public function unshareIpc()
    {
        return $this->push('--unshare-ipc');
    }

    /**
     * Create a new pid namespace.
     *
     * @return $this
     */
    public function unsharePid()
    {
        return $this->push('--unshare-pid');
    }

    /**
     * Create a new network namespace.
     *
     * @return $this
     */
    public function unshareNet()
    {
        return $this->push('--unshare-net');
    }

    /**
     * Create a new uts namespace.
     *
     * @return $this
     */
    public function unshareUts()
    {
        return $this->push('--unshare-uts');
    }

    /**
     * Create a new cgroup namespac.
     *
     * @return $this
     */
    public function unshareCgroup()
    {
        return $this->push('--unshare-cgroup');
    }

    /**
     * Unshare all possible namespaces.
     *
     * @return $this
     */
    public function unshareAll()
    {
        return $this->push('--unshare-all');
    }

    /**
     * Use a custom user id in the sandbox.
     *
     * @param integer|string $uid
     *
     * @return $this
     */
    public function uid($uid)
    {
        return $this->push(['--uid', $uid]);
    }

    /**
     * Use a custom group id in the sandbox.
     *
     * @param integer|string $gid
     *
     * @return $this
     */
    public function gid($gid)
    {
        return $this->push(['--gid', $gid]);
    }

    private function push($flags)
    {
        if (is_array($flags)) {
            $this->flags = array_merge($this->flags, $flags);
        } else {
            $this->flags[] = $flags;
        }

        return $this;
    }
}
