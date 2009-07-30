<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output indent="yes"/>

<xsl:template match="node()|@*">
  <xsl:copy>
    <xsl:apply-templates select="@*|node()" />
  </xsl:copy>
</xsl:template>

<!-- remove technical id attributtes -->
<xsl:template match="@metadata_id" />
<xsl:template match="@lang_metadata_id" />
<xsl:template match="@original_id" />
<xsl:template match="@catalog_metadata_id" />
<xsl:template match="@entry_metadata_id" />
<xsl:template match="@override_id" />
<xsl:template match="@string_override_id" />
<xsl:template match="@language_override_id" />

</xsl:stylesheet>