MyFlaRealtyMatrixPHRETS
=======================

example PHP code to access My Florida Realty Matrix RETS using modified PHRETS code.

#ISSUE#

The relatively new Matrix RETS system created by My Florida Regional MLS require requires the use of POST and not GET.  Unfortunately, the popular PHRETS PHP class library for accessing RETS data doesn't allow POST requests, so a modified version is needed.

This small repos contains the modified PHRETS code and a quick sample of how to call the SearchQuery() method to use POST and not GET.  

The code is pretty self-explainitory so just leave any questions you probably won't have here and I'll do my best to answer.
