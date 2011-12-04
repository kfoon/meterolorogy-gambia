#!/usr/bin/env perl

use strict;
use DBI;
use Device::Modem;
my($dbh)=DBI->connect("DBI:mysql:database=weather;host=localhost", "weather", "w12345");
my $modem = new Device::Modem( port => '/dev/ttyUSB2' );
my($no,$dummy,$mobile,$add);
if( $modem->connect( baudrate => 9600 ) ) { print "connected!\n";
} else { print "sorry, no connection with serial port!\n"; exit; }

$modem->attention();          # send `attention' sequence (+++)
$modem->atsend( 'AT+CMGF=1' . Device::Modem::CR );
print $modem->answer();
$modem->atsend( 'AT+CMGL="ALL"' . Device::Modem::CR );
my @answer=split /\n/,$modem->answer();
for(my $i=0;$i<=$#answer;$i++) {
#  print "$answer[$i]\n";	
#	weather($dbh,$mobile,$answer[$i+1]);
#OK+CMGL: 0,"REC READ","+2203100131",,"11/12/04,11:46:53+00"
  my($no,$dummy,$mobile)=split /\,/,$answer[$i];
	$no=~s/\+CMGL\:\ //;
	if($mobile=~s/\+|\"//g) {
		print "\tM $no: $mobile: $answer[$i+1]\n";
		$modem->atsend( "AT+CMGD=$no" . Device::Modem::CR );
		print "\nDELETE ".$modem->answer();
		$add=weather($dbh,$mobile,$answer[$i+1]);
#		if($add) {
			print "$add\n";
#			$add=qq(AT+CMGS="+$mobile"). Device::Modem::CR ."$add\026". Device::Modem::CR;
#			$modem->atsend($add);
#			print "\n$add\n\tANSWER ".$modem->answer();
#		}
	}
	$i++;
}
$dbh->disconnect();

sub weather {
	my($dbh,$mobile,$argv)=@_;
	my($ret);
	print STDERR "$mobile: $argv\n";
	my($ins)=$dbh->prepare("INSERT INTO `weather`.`data` (`data` ,`orgin`) VALUES (?, ?)");
	my($sel)=$dbh->prepare("SELECT * FROM `weather`.`allow` WHERE `mobile` = ?");
	my(@argv)=split /\ /,$argv;
	my($argc)=$#argv;
	if($sel->execute($mobile)=~/0E0/){ return "NO PERMISSION\n"; }	
	unless($argc==14) { return "Amount of Fields are not proper ($argc)\n"; }	
#	unless(length($argv[7]==3)){
#		print "Fields ($argv[7] is wrong ($argc)\n";
#		exit;
#	}	
	if($ins->execute($argv,$mobile)) { return "$argv saved"; } else { return "DB Error";}		
}
