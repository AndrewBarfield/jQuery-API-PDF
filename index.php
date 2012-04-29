<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* jQuery-API-PDF
*
* Generates a PDF from the jQuery Raw XML API Dump. The PDF features a table
* of contents and index. When the PDF is successfully created it is cached
* server-side for some customizable time. Accessing the script during that
* period results in an HTTP 302 redirection to the PDF.
*
* PHP version 5
*
* LICENSE: This source file is subject to version 3.01 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_01.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @package    jQuery-API-PDF
* @author     Andrew Barfield <barfield2256@gmail.com>
* @copyright  2011-2012 Andrew Barfield
* @license    http://www.php.net/license/3_01.txt  PHP License 3.01
* @version    SVN: $Id$
* @link       http://pear.php.net/package/jQuery-API-PDF
*/



/**
 * 
 */
require('../../shared/scripts/fpdf/fpdf.php');



/**
 * Number of seconds to use cached XML and PDF files.
 * The purpose of the cache is to save bandwidth and increase response time.
 * Note: 86400 seconds is one day.
 */
$cache_maxage		= 86400 * 5;



/**
 * Current time used to determine cache expiration.
 */
$current_time		= time();



/**
 * Filename given to the generated PDF
 */
$pdf_cache_filename	= "jQuery_API_Doc.pdf";



/**
 * Time when the content of the PDF was changed.
 */
$pdf_cache_modtime	= 0;



/**
 * Table of contents array contains bookmarks for each API entry.
 */
$outlines			= array();



/**
 * Root object for the table of contents.
 */
$OutlineRoot		= null;



/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   CategoryName
 * @package    PackageName
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      Class available since Release 1.0.0
 */
class PDF extends FPDF
{

	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;	
	var $NormalTextLineHeight;
	

	
	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function PDF($orientation='P',$unit='mm',$format='A4',$_title,$_url) {
		$this->FPDF($orientation,$unit,$format);
		$this->articletitle=$_title;
		$this->articleurl=$_url;
		$this->fontlist=array("Helvetica","Courier");
		$this->AliasNbPages();
		$this->ResetHTMLParser();
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function ResetHTMLParser() {
		$this->NormalTextLineHeight=4;
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
		$this->tableborder=0;
		$this->tdbegin=false;
		$this->tdwidth=0;
		$this->tdheight=0;
		$this->tdalign="L";
		$this->tdbgcolor=false;
		$this->oldx=0;
		$this->oldy=0;
		$this->PRE=false;
		$this->issetfont=false;
		$this->issetcolor=false;
		$this->SetNormalTextColor();
		$this->SetNormalTextFont();
	}

	
	
	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function SetNormalTextColor() {
		$this->mySetTextColor(32,32,32);
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function SetNormalTextFont() {
		$this->SetFont('Helvetica','',8);
		$this->SetFontSize(8);
		$this->SetStyle('U',false);
		$this->SetStyle('B',false);
		$this->SetStyle('I',false);			
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function WriteHTML($html) {
		$html = str_replace('&trade;','™',$html);
		$html = str_replace('&copy;','©',$html);
		$html = str_replace('&euro;','€',$html);
		
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		$skip=false;
		
		foreach($a as $i=>$e)
		{
			if (!$skip)
			{
			
				if($this->HREF)
					$e=str_replace("\n","",str_replace("\r","",$e));

				if( $i % 2 == 0 )
				{
						// new line
						if($this->PRE)
							$e=str_replace("\r","\n",$e);
						else {
							$e=str_replace("\n","",$e);
							$e=str_replace("\r","",$e);
						}
						
						//Text
						if($this->HREF)
						{
							$this->PutLink($this->HREF,$e);
							$skip=true;
						}
						elseif($this->tdbegin) {
							if(trim($e)!='' && $e!="&nbsp;") {
								$this->Cell(
									$this->tdwidth,
									$this->tdheight,
									$e,
									$this->tableborder,
									'',
									$this->tdalign,$this->tdbgcolor
								);
							}
							elseif($e=="&nbsp;") {
								$this->Cell(
									$this->tdwidth,
									$this->tdheight,
									'',
									$this->tableborder,
									'',
									$this->tdalign,
									$this->tdbgcolor
								);
							}
						}
						else
						{
							$e=str_replace("  "," ",$e);
							$e=str_replace("\t","",$e);
						
							if (strlen(trim($e)) > 0) {

								$txtentities = strtr(
									$e,
									array_flip(
										get_html_translation_table(HTML_ENTITIES)
										)
								);
							
								$this->Write(
									$this->NormalTextLineHeight,
									stripslashes( $txtentities )
								);
							}
						}
				}
				else
				{
						//Tag
						if (substr(trim($e),0,1)=='/')
							$this->CloseTag(
								strtoupper(substr($e,strpos($e,'/')+1))
							);
						else {
							//Extract attributes
							$a2=explode(' ',$e);
							$tag=strtoupper(array_shift($a2));
							$attr=array();
							foreach($a2 as $v) {
								if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
									$attr[strtoupper($a3[1])]=$a3[2];
							}
							$this->OpenTag($tag,$attr);
						}
				}
			} else {
				$this->HREF='';
				$skip=false;
			}
		}

		
		// Reset
		//$this->ResetHTMLParser();
	}

	

	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/		
	function OpenTag($tag,$attr) {
		//Opening tag
		switch($tag){
		
        case 'TABLE':
			$this->Ln($this->NormalTextLineHeight * 2);
			$this->tableborder=1;
            break;
        case 'TR':
            break;
			
        case 'TH':
        case 'TD':
            if( !empty($attr['WIDTH']) )
				$this->tdwidth=($attr['WIDTH']/4);
            else
				$this->tdwidth=60;
				
            if( !empty($attr['HEIGHT']) )
				$this->tdheight=($attr['HEIGHT']/6);
            else
				$this->tdheight=6;
				
            if(!empty($attr['ALIGN'])) {
                $align=$attr['ALIGN'];        
                if($align=='LEFT') $this->tdalign='L';
                if($align=='CENTER') $this->tdalign='C';
                if($align=='RIGHT') $this->tdalign='R';
            }
            else $this->tdalign='L';
            $this->tdbegin=true;
            break;		
		
			case 'CODE':
				$this->SetFont('Courier','',8);
				$this->SetFontSize(8);
				$this->SetStyle('B',false);
				$this->SetStyle('I',false);
				break;
			case 'STRONG':
			case 'B':
				$this->SetStyle('B',true);
				break;
			case 'H1':
				$this->Ln($this->NormalTextLineHeight * 2);
				$this->SetFontSize(16);
				break;
			case 'H2':
				$this->Ln($this->NormalTextLineHeight * 2);
				$this->SetFontSize(14);
				//$this->SetStyle('U',true);
				break;
			case 'H3':
				$this->Ln($this->NormalTextLineHeight * 2);
				$this->SetFontSize(12);
				//$this->SetStyle('U',true);
				break;
			case 'H4':
				$this->Ln($this->NormalTextLineHeight * 2);
				$this->SetFontSize(10);
				$this->SetStyle('B',true);
				break;
			case 'PRE':
				$this->Ln($this->NormalTextLineHeight * 2);
				$this->SetFont('Courier','',8);
				$this->SetFontSize(8);
				$this->SetStyle('B',false);
				$this->SetStyle('I',false);
				$this->PRE=true;
				break;
			case 'BLOCKQUOTE':
				$this->mySetTextColor(139,0,0);
				break;
			case 'I':
			case 'EM':
				$this->SetStyle('I',true);
				break;
			case 'U':
				$this->SetStyle('U',true);
				break;
			case 'A':
				if ($attr['HREF'][0]=='/') {
					$this->HREF='http://api.jquery.com'.$attr['HREF'];
				} else {
					$this->HREF=$attr['HREF'];				
				}
				break;
			case 'IMG':
				/*
				if( isset($attr['SRC']) )
				{
					if(!isset($attr['WIDTH']))
						$attr['WIDTH'] = 0;
					if(!isset($attr['HEIGHT']))
						$attr['HEIGHT'] = 0;
					$this->Image('http://api.jquery.com'.$attr['SRC'], $this->GetX(), $this->GetY() );
					$this->Ln( $this->NormalTextLineHeight );
				}
				*/
				break;
			case 'LI':
				$this->Ln($this->NormalTextLineHeight);
				$this->Write($this->NormalTextLineHeight,'     • ');
				break;
			case 'BR':
				$this->Ln($this->NormalTextLineHeight);
				break;
			case 'P':
				$this->Ln($this->NormalTextLineHeight * 2);
				break;
			case 'HR':
				$this->PutLine();
				break;
			case 'FONT':
				if (isset($attr['FACE']) && 
					in_array(strtolower($attr['FACE']), $this->fontlist)) {
					$this->SetFont(strtolower($attr['FACE']));
					$this->issetfont=true;
				}
				break;
		}
	}

	

	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/		
	function CloseTag($tag) {
		//Closing tag

		if($tag=='TH' || $tag=='TD') {
			$this->tdbegin=false;
			$this->tdwidth=0;
			$this->tdheight=0;
			$this->tdalign="L";
			$this->tdbgcolor=false;
		}

		if($tag=='TR') {
			$this->Ln();
		}

		if($tag=='TABLE') {
			$this->tableborder=0;
		}

		if ($tag=='H1' || $tag=='H2' || $tag=='H3' || $tag=='H4') {
			$this->Ln($this->NormalTextLineHeight);
			$this->SetNormalTextFont();
			$this->SetNormalTextColor();
		}
		
		if ($tag=='PRE') {
			$this->SetNormalTextFont();
			$this->PRE=false;
		}

		if ($tag=='CODE') {
			$this->SetNormalTextFont();
		}
		
		if ($tag=='BLOCKQUOTE') {
			$this->SetNormalTextFont();
			$this->SetNormalTextColor();
		}

		if($tag=='STRONG')
			$tag='B';

		if($tag=='EM')
			$tag='I';

		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
			
		if($tag=='A')
			$this->HREF='';
			
		if($tag=='FONT') {
			if ($this->issetcolor==true) {
				$this->SetNormalTextColor();
			}
			
			if ($this->issetfont) {
				$this->SetNormalTextFont();
				$this->issetfont=false;
			}
		}

	}


	
	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function SetStyle($tag,$enable) {
		$this->$tag = ($enable ? 1 : 0);
		$style='';
		foreach(array('B','I','U') as $s){
			if($this->$s > 0)
				$style.=$s;
		}
		$this->SetFont('',$style);
	}


	
	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function PutLink($URL,$txt) {

		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		
		$this->Write($this->NormalTextLineHeight,$txt,$URL);
		
		$this->SetStyle('U',false);
		$this->mySetTextColor(-1);
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/		
	function PutLine() {
		$this->Ln(2);
		$this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
		$this->Ln(3);
	}

	

	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function mySetTextColor($r,$g=0,$b=0){
		static $_r=0, $_g=0, $_b=0;

		if ($r==-1) 
			$this->SetTextColor($_r,$_g,$_b);
		else {
			$this->SetTextColor($r,$g,$b);
			$_r=$r;
			$_g=$g;
			$_b=$b;
		}
	}	

	

	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/		
	function Header() {

		if ( $this->PageNo() > 1)
		{
			global $title;

			$this->SetStyle('U',false);
			$this->SetStyle('B',false);
			$this->SetStyle('I',false);
			
			// Calculate width of title and position
			$this->SetDrawColor(255,255,255);
			$this->SetFillColor(255,255,255);
			$this->SetTextColor(128,128,128);
			
			// Title
			$this->SetFont('Helvetica','',7);			
			$this->PutLink('http://api.jquery.com',$title.'  ');
			$this->Cell(0,5,date('j F Y'),1,1,'R',true);
			
			// Line break
			$this->Ln($this->NormalTextLineHeight * 2);		
		}
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/		
	function Footer() {
	
		if ( $this->PageNo() > 1)
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Helvetica italic 8
			$this->SetFont('Helvetica','',7);
			// Text color in gray
			$this->SetTextColor(128);
			// Page number
			$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
		}
	}

	

	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function PrintMainCategoryTitle($label) {
		$this->SetTextColor(0,0,0);
		$this->SetFont('Helvetica','B',20);
		$this->SetFillColor(240,240,240);

		$this->Bookmark("$label");
		$this->Cell(0,10,"$label",0,1,'L',true);
		$this->SetFillColor(255,255,255);
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function PrintSubCategoryTitle($label) {
		$this->Ln($this->NormalTextLineHeight);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Helvetica','B',14);

		$this->Bookmark("$label",1,-1);
		$this->Cell(0,6,"$label",0,1,'L',true);
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function PrintEntryHeader($entry, $isMainCategory) {
		$this->Ln($this->NormalTextLineHeight);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Helvetica','B',10);
		
		if ( $isMainCategory )
			$this->Bookmark($entry['name'],1,-1);
		else
			$this->Bookmark($entry['name'],2,-2);
		
		// Entry Title
		if( strcasecmp($entry['type'], 'method') == 0) {
		
			// Iterate and build argument list
			$args = '';
			$thisarg = '';
			foreach ($entry->signature->argument as $entryargs) {
				
				if(!empty($args))
					$args .= ', ';
				
				if(strcasecmp($entryargs['optional'], 'true') == 0)
					$args .= '['.$entryargs['name'].']';
				else
					$args .= $entryargs['name'];
				
			}
			
			if( empty($args) )
				$this->Cell(0,$this->NormalTextLineHeight,$entry['name'].'()',0,1,'L',true);
			else
				$this->Cell(0,$this->NormalTextLineHeight,$entry['name'].'( '.$args.' )',0,1,'L',true);					
		}
		else {
			$this->Cell(0,$this->NormalTextLineHeight,$entry['name'],0,1,'L',true);
		}

		// Short Description
		$txt = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", trim(str_replace(array('<desc>', '</desc>'), '', $entry->desc->asXML())) );
		$this->SetNormalTextColor();
		$this->SetNormalTextFont();
		$this->WriteHTML($txt);

		// Horizontal Rule
		$this->Ln($this->NormalTextLineHeight);
		$this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function PrintArguments($entry) {
		if ( $entry->signature->argument->count() > 0 )
		{
			$this->Ln($this->NormalTextLineHeight);
			$this->SetNormalTextColor();
			$this->SetFont('Helvetica','B',10);
			$this->WriteHTML('Arguments');
		
			foreach ($entry->signature->argument as $entryargs)
			{
				$this->Ln($this->NormalTextLineHeight);
				$this->SetNormalTextFont();
				$desc = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", trim(str_replace(array('<desc>', '</desc>'), '', $entryargs->desc->asXML())) );
				$this->WriteHTML('<b>'.$entryargs['name'].'</b> - '.trim($desc));
			}
		}
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function PrintEntry($txt) {
		$txt = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $txt );
		$this->SetNormalTextColor();
		$this->SetNormalTextFont();
		$this->WriteHTML($txt);
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/	
	function PrintExamples($entry) {
		foreach ($entry->example as $entryexample) {			
			$this->Ln($this->NormalTextLineHeight * 2);
			$this->SetNormalTextColor();
			$this->SetFont('Helvetica','B',10);
			$this->WriteHTML('Example');
		
			$desc = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", trim(str_replace(array('<desc>', '</desc>'), '', $entryexample->desc->asXML())) );
			$code = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", trim(str_replace(array('<code>', '</code>'), '', $entryexample->code->asXML())) );

			$this->Ln($this->NormalTextLineHeight);
			$this->SetNormalTextFont();

			$this->WriteHTML($desc.'<pre>'.$code.'</pre>');
		}

	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function PrintEntries($thiscategory, $entries, $isMainCategory) {		
		foreach ($entries as $entry) {
			foreach ($entry->category as $entrycategory) {
				if( strcasecmp($thiscategory['name'], $entrycategory['name']) == 0 ) {
					$this->PrintEntryHeader( $entry, $isMainCategory );
					$this->PrintArguments( $entry );
					$this->PrintEntry( str_replace(array('<longdesc>', '</longdesc>'), '', $entry->longdesc->asXML()) );
					$this->PrintExamples( $entry );
					$this->Ln($this->NormalTextLineHeight * 2);
				}
			}
		}
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function CreateTitlePage() {
		$this->SetLineWidth(.1);
		$this->AddPage();
		
		$this->SetDrawColor(128,128,128);
		$this->SetFillColor(255,255,255);
		$this->SetNormalTextColor();

		$this->SetFont('Helvetica','',36);
		$this->SetFontSize(36);
		$this->Ln(150);
		$this->Cell(0,5,'jQuery API Documentation',0,1,'C',true);			
		
		$this->SetFont('Helvetica','',12);
		$this->SetFontSize(12);
		$this->Ln(100);
		$this->WriteHTML('        This PDF was generated by <a href="http://jqueryapidoc.andrewbarfield.com/">jqueryapidoc.andrewbarfield.com</a> on');
		$this->Ln(7);
		$this->SetFont('Helvetica','',12);
		$this->SetFontSize(12);
		$this->WriteHTML('        '.date('j F Y').' from the <a href="http://api.jquery.com/api/">Raw XML API Dump</a>.');

		//209.90 x 297.04
		$this->Rect(10, 10, 209.90 - 20, 297.04 - 20, 'D');
		
		// Dimensions in mm
		$w = 148.17;
		$h = 36.34;			
		$this->Image('images/jquery_logo.png',(209.90 - $w)/2,30,$w,$h);	
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function Bookmark($txt, $level=0, $y=0) {
		if($y==-1)
			$y=$this->GetY();
		$this->outlines[]=array('t'=>$txt, 'l'=>$level, 'y'=>($this->h-$y)*$this->k, 'p'=>$this->PageNo());
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function _putbookmarks() {
		$nb=count($this->outlines);
		if($nb==0)
			return;
		$lru=array();
		$level=0;
		foreach($this->outlines as $i=>$o)
		{
			if($o['l']>0)
			{
				$parent=$lru[$o['l']-1];
				//Set parent and last pointers
				$this->outlines[$i]['parent']=$parent;
				$this->outlines[$parent]['last']=$i;
				if($o['l']>$level)
				{
					//Level increasing: set first pointer
					$this->outlines[$parent]['first']=$i;
				}
			}
			else
				$this->outlines[$i]['parent']=$nb;
			if($o['l']<=$level and $i>0)
			{
				//Set prev and next pointers
				$prev=$lru[$o['l']];
				$this->outlines[$prev]['next']=$i;
				$this->outlines[$i]['prev']=$prev;
			}
			$lru[$o['l']]=$i;
			$level=$o['l'];
		}
		//Outline items
		$n=$this->n+1;
		foreach($this->outlines as $i=>$o)
		{
			$this->_newobj();
			$this->_out('<</Title '.$this->_textstring($o['t']));
			$this->_out('/Parent '.($n+$o['parent']).' 0 R');
			if(isset($o['prev']))
				$this->_out('/Prev '.($n+$o['prev']).' 0 R');
			if(isset($o['next']))
				$this->_out('/Next '.($n+$o['next']).' 0 R');
			if(isset($o['first']))
				$this->_out('/First '.($n+$o['first']).' 0 R');
			if(isset($o['last']))
				$this->_out('/Last '.($n+$o['last']).' 0 R');
			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]',1+2*$o['p'],$o['y']));
			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root
		$this->_newobj();
		$this->OutlineRoot=$this->n;
		$this->_out('<</Type /Outlines /First '.$n.' 0 R');
		$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
		$this->_out('endobj');
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function _putresources() {
		parent::_putresources();
		$this->_putbookmarks();
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function _putcatalog() {
		parent::_putcatalog();
		if(count($this->outlines)>0)
		{
			$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
			$this->_out('/PageMode /UseOutlines');
		}
	}		



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function CreateIndex() {
		//Index title
		$this->SetFontSize(14);
		$this->Cell(0,5,'Index',0,1,'C');
		$this->SetFontSize(12);
		$this->Ln(10);

		$size=sizeof($this->outlines);
		$PageCellSize=$this->GetStringWidth('p. '.$this->outlines[$size-1]['p'])+2;
		for ($i=0;$i<$size;$i++){
			//Offset
			$level=$this->outlines[$i]['l'];
			if($level>0)
				$this->Cell($level*8);

			//Caption
			$str=$this->outlines[$i]['t'];
			$strsize=$this->GetStringWidth($str);
			$avail_size=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-4;
			while ($strsize>=$avail_size) {
				$str=substr($str,0,-1);
				$strsize=$this->GetStringWidth($str);
			}
			$this->Cell($strsize+2,$this->FontSize+2,$str);

			//Filling dots
			$w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+2);
			$nb=$w/$this->GetStringWidth('.');
			$dots=str_repeat('.',$nb);
			$this->Cell($w,$this->FontSize+2,$dots,0,0,'R');

			//Page number
			$this->Cell($PageCellSize,$this->FontSize+2,$this->outlines[$i]['p'],0,1,'R');
		}
	}



	/**
	* ?????
	*
	* Summaries should add description beyond the method's name. The
	* best method names are "self-documenting", meaning they tell you
	* basically what the method does.  If the summary merely repeats
	* the method name in sentence form, it is not providing more
	* information.
	*
	* @param string $arg1 the string to quote
	*
	* @return int the integer of the set mode used. FALSE if foo
	*             foo could not be set.
	* @throws exceptionclass [description]
	*
	* @access public
	* @static
	* @see Net_Sample::$foo, Net_Other::someMethod()
	* @since Method available since Release 1.0.0
	*/
	function CreatejQueryDocumentation() {

		global $cache_maxage;
		global $current_time;
		global $pdf_cache_filename;
	
		// Locals
		$xml_cache_filename = "api.xml";
		$xml_cache_modifiedtime = 0;

		// If the XML file does not exist then it's modified time is defined as zero
		if (file_exists($xml_cache_filename)) {
				$xml_cache_modifiedtime = filemtime($xml_cache_filename);
		} else {
				$xml_cache_modifiedtime = 0;
		}

		// Use existing XML file for $cache_maxage (in seconds) before downloading again.
		if($current_time - $xml_cache_modifiedtime >= $cache_maxage) {
			// Download XML file
			$xml = simplexml_load_file("http://api.jquery.com/api/","SimpleXMLElement",LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE);

			// Save file
			$fp = fopen($xml_cache_filename, 'w');
			fwrite($fp, $xml->asXML());
			fclose($fp);
		} else {
			// Use the cached XML file
			$xml = simplexml_load_file("api.xml","SimpleXMLElement",LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE);
		}

		// Build the PDF
		$this->CreateTitlePage();
		foreach ($xml->categories->category as $MainCategory) {		
			// Main Category
			$this->AddPage();
			$this->PrintMainCategoryTitle( $MainCategory['name'] );
			$this->PrintEntries($MainCategory, $xml->entries->entry, true);

			// Subcategories
			if( $MainCategory->count() > 0)
			{
				foreach ($MainCategory->category as $subcategory)
				{
					$this->PrintSubCategoryTitle( $subcategory['name'] );
					$this->PrintEntries($subcategory, $xml->entries->entry, false);
				}
			}

			// Skip 3 lines
			$this->Ln($this->NormalTextLineHeight * 3);
		}
		
		//Index
		$this->AddPage();
		$this->Bookmark('Index');
		$this->CreateIndex();	
		
		//Save PDF to file
		$this->Output($pdf_cache_filename, 'F');

		//Redirect
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header('Location: '.$pdf_cache_filename);	
	}
}



// If the PDF does not exist then it's modified time is defined as zero
if (file_exists($pdf_cache_filename)) {
		$pdf_cache_modtime = filemtime($pdf_cache_filename);
} else {
		$pdf_cache_modtime = 0;
}

// Use existing PDF file for $cache_maxage (in seconds) before generating again
if($current_time - $pdf_cache_modtime <= $cache_maxage) {
	// Redirect to cached PDF
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	header('Location: '.$pdf_cache_filename);
	exit;
} else {
	// Create new PDF
	$pdf=new PDF('P','mm','A4','jQuery API Documentation','http://jqueryapidoc.andrewbarfield.com/',false);
	$pdf->SetCompression(true);
	$title = 'jQuery API Documentation';
	$pdf->SetTitle($title);
	$pdf->SetAuthor('andrewbarfield.com');
	$pdf->CreatejQueryDocumentation();
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
 
?>
