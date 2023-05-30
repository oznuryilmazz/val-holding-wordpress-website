#!/usr/bin/perl
#
# Tribulant WordPress Newsletter plugin "wpmlbounce.cgi"
# Upload to your "cgi-bin" directory and chmod to 0755 so that it is executable

# Absolute URL to your Wordpress installation. No trailing slash
$wordpress_url = "https://domain.com";

# Program and paramaters to pass URL to
# Usually works as default (may be /usr/local/bin/wget or other)
$http_program = "/usr/bin/wget -O /dev/null " . $wordpress_url . "/index.php";

###########################################
# dont need to change anything below here #
###########################################

sub encode {
  my $str = shift || '';
  $str =~ s/([^\w.-])/sprintf("%%%02X",ord($1))/eg;
  $str;
}

# get piped message
$email = '';
while($line=<>){
 $email = $email . $line;
}

if($email){
 if($email ne ''){
  $sys_cmd = $http_program . "\\?wpmlmethod=bounce\\&em=" . encode(substr($email,0,4096)) . " 1> /dev/null 2> /dev/null";
 }
 # forward to bounce.php
 system($sys_cmd);
}

# That's all there is to it!!!