<?php

use PHPUnit\Framework\TestCase;

use Mtownsend\ArrayRedactor\ArrayRedactor;

class ArrayRedactorTest extends TestCase
{
    protected function setUp()
    {
    	$this->content = [
    		'email' => 'mtownsend5512@gmail.com',
    		'password' => 'secret123',
    		'changes' => [
    			'account' => [
					'old_password' => 'secret321',
		    		'new_password' => 'secret789'
    			],
    		]
    	];
    }

    /** @test */
    public function can_instantiate_class_through_helper()
    {
    	$result = array_redactor($this->content, ['password'], '[REDACTED]')->redact();
        $this->assertEquals(array_merge($this->content, [
        	'password' => '[REDACTED]'
        ]), $result);
    }

    /** @test */
    public function can_accept_array_content()
    {
    	$result = (new ArrayRedactor($this->content, ['password'], '[REDACTED]'))->redact();
        $this->assertEquals(array_merge($this->content, [
        	'password' => '[REDACTED]'
        ]), $result);
    }

    /** @test */
    public function can_accept_json_content()
    {
    	$result = (new ArrayRedactor(json_encode($this->content), ['password'], '[REDACTED]'))->redact();
        $this->assertEquals(array_merge($this->content, [
        	'password' => '[REDACTED]'
        ]), $result);
    }

    /** @test */
    public function can_be_invoked()
    {
    	$result = new ArrayRedactor($this->content, ['password'], '[REDACTED]');
        $this->assertEquals(array_merge($this->content, [
        	'password' => '[REDACTED]'
        ]), $result());
    }

    /** @test */
    public function can_be_cast_as_json()
    {
    	$result = new ArrayRedactor($this->content, ['password'], '[REDACTED]');
        $this->assertEquals(json_encode(array_merge($this->content, [
        	'password' => '[REDACTED]'
        ])), (string) $result);
    }

    /** @test */
    public function can_redact_nested_keys()
    {
    	$result = (new ArrayRedactor($this->content, ['old_password', 'new_password'], '[REDACTED]'))->redact();
        $this->assertEquals(array_merge($this->content, [
        	'changes' => ['account' => [
	        	'old_password' => '[REDACTED]',
	        	'new_password' => '[REDACTED]'
        	]]
        ]), $result);
    }

    /** @test */
    public function can_change_redaction_ink()
    {
    	$result = (new ArrayRedactor($this->content, ['password'], null))->redact();
        $this->assertEquals(array_merge($this->content, [
        	'password' => null
        ]), $result);
    }

    /** @test */
    public function can_omit_constructor_arguments_in_favor_of_methods()
    {
    	$result = (new ArrayRedactor())->content($this->content)->keys(['password'])->ink(null)->redact();
        $this->assertEquals(array_merge($this->content, [
        	'password' => null
        ]), $result);
    }

    /** @test */
    public function can_accept_closure_in_ink()
    {
        $array = $this->content;
        $result = (new ArrayRedactor())->content($this->content)->keys(['email'])->ink(function() use ($array) {
            return substr($array['email'], stripos($array['email'], '@'));
        })->redact();
        $this->assertEquals(array_merge($this->content, [
            'email' => '@gmail.com'
        ]), $result);
    }
}
