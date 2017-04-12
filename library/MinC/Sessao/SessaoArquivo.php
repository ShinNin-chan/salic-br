<?php
/*
* Created on 03/05/2010
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
*/

class SessaoArquivo
{

	private static $instance;
	
	public $namespace = null;
	
	// Contrutor da sess�o, respons�vel pela inicializa��o
	private function __construct()
	{
		Zend_Session::start();

		$this->namespace = new Zend_Session_Namespace('arquivo');
		
		if( !isset( $this->namespace->initialized ) )
		{
			Zend_Session::regenerateId();

			$this->namespace->initialized = true;
		}
	}

	// Singleton para verificar se sess�o j� foi inicializada
	public static function getInstance()
	{
		if( !isset( self::$instance ) )
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	// Recebe os valores da sess�o
	public function getSessVar( $var , $default = null )
	{

		if( isset( $this->namespace->$var ) )
		{
			return $this->namespace->$var;
		}
		else
		{
			return $default;
		}
	}

	// Seta os valores na sess�o
	public function setSessVar( $var , $value )
	{
		if( !empty( $var ) && !empty( $value ) )
		{
			$this->namespace->$var = $value;
		}
	}

	// Mata a sess�o arquivo
	public static function emptySess()
	{
		
		Zend_Session::namespaceUnset('arquivo');
	}

}

?>