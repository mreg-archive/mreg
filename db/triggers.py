#!/usr/bin/python
#coding=utf8

def main():
    """Create and output sql commands for creating mreg triggers"""

    # aux__Address
    address = AuxAddress('aux__Address')
    address.setEtagAuxLookup(False)
    address.addEtagCol('mailee')
    address.addEtagCol('mailee_role_descriptor')
    address.addEtagCol('thoroughfare')
    address.addEtagCol('plot')
    address.addEtagCol('littera')
    address.addEtagCol('stairwell')
    address.addEtagCol('floor')
    address.addEtagCol('door')
    address.addEtagCol('supplementary_delivery_point_data')
    address.addEtagCol('delivery_service')
    address.addEtagCol('alternate_delivery_service')
    address.addEtagCol('postcode')
    address.addEtagCol('town')
    address.addEtagCol('country_code')
    address.addRevisionCol('mailee')
    address.addRevisionCol('mailee_role_descriptor')
    address.addRevisionCol('thoroughfare')
    address.addRevisionCol('plot')
    address.addRevisionCol('littera')
    address.addRevisionCol('stairwell')
    address.addRevisionCol('floor')
    address.addRevisionCol('door')
    address.addRevisionCol('supplementary_delivery_point_data')
    address.addRevisionCol('delivery_service')
    address.addRevisionCol('alternate_delivery_service')
    address.addRevisionCol('postcode')
    address.addRevisionCol('town')
    address.addRevisionCol('country_code')

    # aux__Mail
    mail = Aux('aux__Mail')
    mail.setEtagAuxLookup(False)
    mail.addEtagCol('mail')
    mail.addEtagCol('name')
    mail.revSetRefCol('name')
    mail.addRevisionCol('mail')

    # aux__Phone
    phone = AuxPhone('aux__Phone')
    phone.setEtagAuxLookup(False)
    phone.addEtagCol('cc')
    phone.addEtagCol('ndc')
    phone.addEtagCol('sn')
    phone.addEtagCol('carrier')
    phone.addEtagCol('name')

    # dir__Employer
    employer = Dir('dir__Employer') 
    employer.addEtagCol('name')
    employer.addEtagCol('parentId')
    employer.addEtagCol('corporateId')
    employer.addRevisionCol('name')
    employer.addRevisionCol('parentId')
    employer.addRevisionCol('corporateId')

    # dir__Faction
    faction = Dir('dir__Faction')
    faction.addEtagCol('parentId')
    faction.addEtagCol('name')
    faction.addEtagCol('type')
    faction.addEtagCol('description')
    faction.addEtagCol('notes')
    faction.addEtagCol('plusgiro')
    faction.addEtagCol('bankgiro')
    faction.addEtagCol('url')
    faction.addRevisionCol('parentId')
    faction.addRevisionCol('name')
    faction.addRevisionCol('type')
    faction.addRevisionCol('description')
    faction.addRevisionCol('notes')
    faction.addRevisionCol('plusgiro')
    faction.addRevisionCol('bankgiro')
    faction.addRevisionCol('url')

    # dir__Member
    member = Dir('dir__Member')
    member.addEtagCol('personalId')
    member.addEtagCol('names')
    member.addEtagCol('surname')
    member.addEtagCol('paymentType')
    member.addEtagCol('bankAccount')
    member.addEtagCol('notes')
    member.addEtagCol('salary')
    member.addRevisionCol('personalId')
    member.addRevisionCol('names')
    member.addRevisionCol('surname')
    member.addRevisionCol('paymentType')
    member.addRevisionCol('bankAccount')
    member.addRevisionCol('notes')
    member.addRevisionCol('salary')

    # dir__Workplace
    workplace = Dir('dir__Workplace')
    workplace.addEtagCol('parentId')
    workplace.addEtagCol('name')
    workplace.addEtagCol('unions')
    workplace.addEtagCol('collectiveAgreements')
    workplace.addEtagCol('employees')
    workplace.addRevisionCol('parentId')
    workplace.addRevisionCol('name')
    workplace.addRevisionCol('unions')
    workplace.addRevisionCol('collectiveAgreements')
    workplace.addRevisionCol('employees')

    # eco__MemberInvoice
    memberInvoice = Dir('eco__MemberInvoice')
    memberInvoice.addEtagCol('recipientId')
    memberInvoice.addEtagCol('payerId')
    memberInvoice.addEtagCol('amount')
    memberInvoice.addEtagCol('ocr')
    memberInvoice.addEtagCol('isAutogiro')
    memberInvoice.addEtagCol('paidVia')
    memberInvoice.addEtagCol('tExpire')
    memberInvoice.addEtagCol('tPrinted')
    memberInvoice.addEtagCol('tPaid')
    memberInvoice.addEtagCol('tExported')
    memberInvoice.addEtagCol('description')
    memberInvoice.addEtagCol('template')
    memberInvoice.addEtagCol('verification')
    memberInvoice.addEtagCol('title')
    memberInvoice.addRevisionCol('recipientId')
    memberInvoice.addRevisionCol('payerId')
    memberInvoice.addRevisionCol('amount')
    memberInvoice.addRevisionCol('isAutogiro')
    memberInvoice.addRevisionCol('paidVia')
    #memberInvoice.addRevisionCol('tExpire')
    #memberInvoice.addRevisionCol('tPrinted')
    #memberInvoice.addRevisionCol('tPaid')
    #memberInvoice.addRevisionCol('tExported')
    memberInvoice.addRevisionCol('description')
    memberInvoice.addRevisionCol('title')

    # sys__Group
    group = Dir('sys__Group')
    group.setEtagAuxLookup(False)
    group.setIdCol('name')
    group.addEtagCol('description')
    group.addRevisionCol('description')

    # sys__Setting
    setting = Dir('sys__Setting')
    setting.setEtagAuxLookup(False)
    setting.setIdCol('name')
    setting.addEtagCol('value')
    setting.addEtagCol('comment')
    setting.addRevisionCol('value')
    setting.addRevisionCol('comment')

    # sys__User
    user = User('sys__User')
    user.setIdCol('uname')
    user.addEtagCol('status')
    user.addEtagCol('password')
    user.addEtagCol('fullname')
    user.addEtagCol('notes')
    user.addRevisionCol('status')
    user.addRevisionCol('fullname')
    user.addRevisionCol('notes')

    # sys__Setting
    template = Dir('Template')
    template.setEtagAuxLookup(False)
    template.setIdCol('name')
    template.addEtagCol('headline')
    template.addEtagCol('tmpl')
    template.addRevisionCol('headline')
    template.addRevisionCol('tmpl')

    facFac = Xref('xref__Faction_Faction');
    facWork = Xref('xref__Faction_Workplace');
    workMem = Xref('xref__Workplace_Member');
    facMem = XrefFacMem('xref__Faction_Member');

    # Output
    print 'DELIMITER //'
    print address
    print mail
    print phone

    print employer
    print faction
    print member
    print workplace

    print memberInvoice

    print group
    print setting
    print user
    
    print template

    print facFac
    print facWork
    print workMem
    print facMem
    print 'DELIMITER ;'



# Basic trigger objects
class Trigger():
    """
        A Trigger object represents the SQL for one mysql trigger function
    """

    def __init__(self, trgType, table):
        """Create trigger, set type (eg. BEFORE UPDATE..) and table name."""
        self.trgType = trgType.upper()
        self.table = table
        self.name = table + '_' + self.trgType.lower().replace(' ', '_');
        self.content = ''

    def addLine(self, content):
        """Add a line to trigger content. Call multiple times if needed."""
        self.content += '\t' + content + '\n'

    def prependLine(self, content):
        """Prepend a line to trigger content. Call multiple times if needed."""
        self.content = '\t' + content + '\n' + self.content

    def __str__(self):
        """Get trigger as SQL string"""
        if ( self.content == '' ):
            return ''
        sql = '\nDROP TRIGGER IF EXISTS `' + self.name + '`//\n'
        sql += 'CREATE TRIGGER `' + self.name + '` ' + self.trgType + ' ON `' + self.table + '`\n'
        sql += 'FOR EACH ROW\n'
        sql += 'BEGIN\n'
        sql += self.content
        sql += 'END//\n'
        return sql



# Basic table class
class TriggerTable():
    """
        A TriggerTable is a db table carrying triggers
    """

    def __init__(self, table):
        """Set db table name"""
        self.table = table
        self.idCol = 'id'

    def setIdCol(self, col):
        self.idCol = col

    def beforeInsert(self):
        """Create empty before insert trigger"""
        return Trigger('BEFORE INSERT', self.table)

    def afterInsert(self):
        """Create empty after insert trigger"""
        return Trigger('AFTER INSERT', self.table)

    def beforeUpdate(self):
        """Create empty before update trigger"""
        return Trigger('BEFORE UPDATE', self.table)

    def afterUpdate(self):
        """Create empty after update trigger"""
        return Trigger('AFTER UPDATE', self.table)

    def beforeDelete(self):
        """Create empty before delete trigger"""
        return Trigger('BEFORE DELETE', self.table)

    def afterDelete(self):
        """Create empty after delete trigger"""
        return Trigger('BEFORE DELETE', self.table)

    def __str__(self):
        """Get all triggers as sql strings"""
        sql = self.beforeInsert().__str__()
        sql += self.afterInsert().__str__()
        sql += self.beforeUpdate().__str__()
        sql += self.afterUpdate().__str__()
        sql += self.beforeDelete().__str__()
        sql += self.afterDelete().__str__()
        return sql



class EtagTable(TriggerTable):
    """
        Etag tables have a col named etag that should be
        updated on every write to resource MD5 hash
    """
    def __init__(self):
        self.etagCols = []
        self.doAuxLookup = True
    
    def addEtagCol(self, col):
        """Add column to calculate etag from"""
        self.etagCols.append(col)

    def setEtagAuxLookup(self, flag):
        """Set if etags from aux tables should be included in etag"""
        self.doAuxLookup = flag

    def beforeInsert(self):
        """Create initial etag before insert"""
        t = TriggerTable.beforeInsert(self)
        if ( len(self.etagCols) > 0 ):
            etag = '`, NEW.`'.join(self.etagCols)
            etag = "SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`" + etag + "`));"
            t.addLine(etag);
        return t

    def beforeUpdate(self):
        """Update etag before update."""
        t = TriggerTable.beforeUpdate(self)

        if ( len(self.etagCols) > 0 ):
            etag = '`, NEW.`'.join(self.etagCols)
            etag = "SET NEW.`etag`=MD5(CONCAT_WS('', NEW.`" + etag + "`"
            t.addLine(etag + '));')
        return t



class RevisionTable(TriggerTable):
    """
        A Revision should get some or all edits saved
        to the revisions table
    """
    def __init__(self):
        self.revisionCols = []
        self.refCol = False

    def addRevisionCol(self, col):
        self.revisionCols.append(col)

    def revSetRefCol(self, col):
        self.refCol = col

    def afterUpdate(self):
        t = TriggerTable.afterUpdate(self)
        for col in self.revisionCols:
            if ( self.refCol ):
                reference = "NEW.`" + self.refCol + "`"
            else :
                reference = "'" + col + "'"
            t.addLine("IF ( OLD.`" + col + "` != NEW.`" + col + "` ) THEN")
            t.addLine("\tINSERT INTO `aux__Revision` SET")
            t.addLine("\t\t`tModified`=UNIX_TIMESTAMP(),")
            t.addLine("\t\t`ref_table`='" + self.table + "',")
            t.addLine("\t\t`ref_id`=NEW.`" + self.idCol + "`,")
            t.addLine("\t\t`ref_column`=" + reference + ",")
            t.addLine("\t\t`modifiedBy`=NEW.`modifiedBy`,")
            t.addLine("\t\t`old_value`=OLD.`" + col + "`,")
            t.addLine("\t\t`new_value`=NEW.`" + col + "`;")
            t.addLine("END IF;")
        return t



class TimeTable(EtagTable):
    """
        A TimeTable keeps track of tCreated and tModified times
    """
    
    def beforeInsert(self):
        t = EtagTable.beforeInsert(self)
        t.addLine("IF NEW.`tCreated` = '0' THEN")
        t.addLine("    SET NEW.`tCreated` = UNIX_TIMESTAMP();")
        t.addLine("END IF;")
        t.addLine("SET NEW.`tModified`=NEW.`tCreated`;")
        return t

    def beforeUpdate(self):
        t = EtagTable.beforeUpdate(self)
        t.addLine("SET NEW.`tModified` = UNIX_TIMESTAMP();")
        return t



 
class Dir(TimeTable, RevisionTable):
    """
        Directory tables
        - etag and revision support
        - owner, group and mode cols are etaged and revised
        - tCreated and tModified unix timestamps
    """
    def __init__(self, table):
        TriggerTable.__init__(self, table)
        EtagTable.__init__(self)       
        EtagTable.addEtagCol(self, 'owner')
        EtagTable.addEtagCol(self, 'group')
        EtagTable.addEtagCol(self, 'mode')
        RevisionTable.__init__(self)
        RevisionTable.addRevisionCol(self, 'owner')
        RevisionTable.addRevisionCol(self, 'group')
        RevisionTable.addRevisionCol(self, 'mode')

    def beforeInsert(self):
        t = TimeTable.beforeInsert(self)
        return t

    def beforeUpdate(self):
        t = TimeTable.beforeUpdate(self)
        return t

    def afterUpdate(self):
        t = RevisionTable.afterUpdate(self)
        return t



class User(Dir):
    def beforeInsert(self):
        t = Dir.beforeInsert(self)
        t.addLine("SET NEW.`tLogin` = UNIX_TIMESTAMP();")
        t.addLine("SET NEW.`tLastLogin` = UNIX_TIMESTAMP();")
        return t
      



class Xref(TriggerTable):
    """
        Triggers for x-reference tables
    """
    def beforeInsert(self):
        t = TriggerTable.beforeInsert(self)
        t.addLine("IF NEW.`tSince` = '0' THEN")
        t.addLine("    SET NEW.`tSince` = UNIX_TIMESTAMP();")
        t.addLine("END IF;")
        t.addLine("IF NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN")
        t.addLine("    SET NEW.`tUnto` = UNIX_TIMESTAMP();")
        t.addLine("END IF;")
        return t

    def beforeUpdate(self):
        t = TriggerTable.beforeUpdate(self)
        t.addLine("IF OLD.`state` = 'OK' AND NEW.`state` != 'OK' AND NEW.`tUnto` = '0' THEN")
        t.addLine("    SET NEW.`tUnto` = UNIX_TIMESTAMP();")
        t.addLine("END IF;")
        return t



class XrefFacMem(Xref):
    """
        Some special actions for xref__Faction_Member
    """
    def afterInsert(self):
        """Update member cache after insert"""
        t = TriggerTable.afterInsert(self)
        t.addLine("DECLARE factionType VARCHAR(20);")
        t.addLine("DECLARE factionName VARCHAR(100);")
        t.addLine("SELECT `type`, `name` FROM `dir__Faction`")
        t.addLine("    WHERE `id`= NEW.`master_id`")
        t.addLine("    INTO factionType, factionName;")
        t.addLine("IF factionType = 'LS' AND NEW.`state` = 'OK' THEN")
        t.addLine("    UPDATE `dir__Member`")
        t.addLine("    SET `LS` = factionName")
        t.addLine("    WHERE `id`=NEW.`foreign_id`;")
        t.addLine("END IF;")
        return t

    def afterUpdate(self):
        """Update member cache after update"""
        t = TriggerTable.afterUpdate(self)
        t.addLine("DECLARE factionType VARCHAR(20);")
        t.addLine("DECLARE factionName VARCHAR(100);")
        t.addLine("SELECT `type`, `name` FROM `dir__Faction`")
        t.addLine("    WHERE `id`= NEW.`master_id`")
        t.addLine("    INTO factionType, factionName;")
        t.addLine("IF factionType = 'LS' AND (NEW.`state` = 'OK' OR OLD.`state` = 'OK') THEN")
        t.addLine("    UPDATE `dir__Member`")
        t.addLine("    SET `LS` = factionName")
        t.addLine("    WHERE `id`=NEW.`foreign_id`;")
        t.addLine("END IF;")
        return t



class Aux(TimeTable, RevisionTable):
    """
        Aux table base class standard
        Etag and Revision support
    """
    def __init__(self, table):
        TriggerTable.__init__(self, table)
        EtagTable.__init__(self)       
        RevisionTable.__init__(self)

    def beforeUpdate(self):
        t = TimeTable.beforeUpdate(self)
        return t

    def afterUpdate(self):
        t = RevisionTable.afterUpdate(self)
        return t



class AuxPhone(Aux):        
    def afterUpdate(self):
        t = Aux.afterUpdate(self)
        t.addLine("IF ( OLD.`cc`!=NEW.`cc` OR OLD.`ndc`!=NEW.`ndc` OR OLD.`sn`!=NEW.`sn` ) THEN")
        t.addLine("\tINSERT INTO `aux__Revision` SET")
        t.addLine("\t    `tModified`=UNIX_TIMESTAMP(),")
        t.addLine("\t    `ref_table` = '" + self.table + "',")
        t.addLine("\t    `ref_id` = NEW.`id`,")
        t.addLine("\t    `ref_column` = NEW.`name`,")
        t.addLine("\t    `modifiedBy` = NEW.`modifiedBy`,")
        t.addLine("\t    `old_value` = CONCAT('+', OLD.`cc`, OLD.`ndc`, OLD.`sn`),")
        t.addLine("\t    `new_value` = CONCAT('+', NEW.`cc`, NEW.`ndc`, NEW.`sn`);")
        t.addLine("END IF;")
        return t


class AuxAddress(Aux):
    def prependConditionalDefaults(self, t):
        """Prepend conditional defaults to trigger t"""
        sql = "IF NEW.`mailee` IS NOT NULL AND (NEW.`mailee_role_descriptor` IS NULL OR NEW.`mailee_role_descriptor`='') THEN\n"
        sql += "\t    SET NEW.`mailee_role_descriptor` = 'c/o';\n"
        sql += "\tEND IF;\n"
        sql += "\tIF NEW.`alternate_delivery_service` IS NOT NULL AND NEW.`alternate_delivery_service` != '' AND (NEW.`delivery_service` IS NULL OR NEW.`delivery_service`='') THEN\n"
        sql += "\t    SET NEW.`delivery_service` = 'Box';\n"
        sql += "\tEND IF;\n"
        t.prependLine(sql)
        return t


    def prependCaseing(self, t):
        """Prepend caseing to trigger t"""
        sql = "SET NEW.`delivery_service` = CONCAT(UPPER(SUBSTRING(NEW.`delivery_service`, 1, 1)), LOWER(SUBSTRING(NEW.`delivery_service`, 2)));\n"
        sql += "\tSET NEW.`littera` = UPPER(NEW.`littera`);\n"
        sql += "\tSET NEW.`stairwell` = UPPER(NEW.`stairwell`);\n"
        sql += "\tSET NEW.`floor` = UPPER(NEW.`floor`);\n"
        sql += "\tSET NEW.`town` = CONCAT(UPPER(SUBSTRING(NEW.`town`, 1, 1)), LOWER(SUBSTRING(NEW.`town`, 2)));\n"
        sql += "\tSET NEW.`country_code` = UPPER(NEW.`country_code`);\n"
        t.prependLine(sql)
        return t

    def prependGrouping(self, t):
        """Prepend grouping statements to trigger t"""
        sql = "IF CHAR_LENGTH(NEW.`postcode`) = 5 THEN\n"
        sql += "\t    SET NEW.`postcode` = CONCAT_WS(' ', SUBSTRING(NEW.`postcode`, 1, 3), SUBSTRING(NEW.`postcode`, 4));\n"
        sql += "\tEND IF;\n"
        t.prependLine(sql)
        return t

    def  beforeInsert(self):
        t = Aux.beforeInsert(self)
        t = self.prependGrouping(t)
        t = self.prependCaseing(t)
        t = self.prependConditionalDefaults(t)
        return t

    def  beforeUpdate(self):
        t = Aux.beforeUpdate(self)
        t = self.prependGrouping(t)
        t = self.prependCaseing(t)
        t = self.prependConditionalDefaults(t)
        return t

    def afterUpdate(self):
        t = TriggerTable.afterUpdate(self)
        for col in self.revisionCols:
            t.addLine("IF ( OLD.`" + col + "` != NEW.`" + col + "` ) THEN")
            t.addLine("\tINSERT INTO `aux__Revision` SET")
            t.addLine("\t\t`tModified`=UNIX_TIMESTAMP(),")
            t.addLine("\t\t`ref_table`='" + self.table + "',")
            t.addLine("\t\t`ref_id`=NEW.`" + self.idCol + "`,")
            t.addLine("\t\t`ref_column`= '" + col + "',")
            t.addLine("\t\t`modifiedBy`=NEW.`modifiedBy`,")
            t.addLine("\t\t`old_value`=OLD.`" + col + "`,")
            t.addLine("\t\t`new_value`=NEW.`" + col + "`;")
            t.addLine("END IF;")
        return t


if __name__ == "__main__":
    main()
