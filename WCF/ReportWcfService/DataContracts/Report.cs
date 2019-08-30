using System;
using System.Collections.Generic;
using System.Runtime.Serialization;
using LinqToDB.Mapping;

namespace ReportWcfService
{
    [DataContract]
    [Table("report")]
    public class Report
    {
        [DataMember]
        [Column("id", IsPrimaryKey = true)]
        public Int32 Id { get; set; }

        [DataMember]
        [Column("apprenticeid")]
        public Int32 ApprenticeId { get; set; }

        [DataMember]
        [Column("year")]
        public Int32 Year { get; set; }

        [DataMember]
        [Column("comment")]
        public String Comment { get; set; }

        [DataMember]
        [Column("verifiedby")]
        public Int32? VerifiedBy { get; set; }

        [DataMember]
        [Column("begin")]
        public DateTime Begin { get; set; }

        [DataMember]
        [Column("end")]
        public DateTime End { get; set; }

        [DataMember]
        public List<Day> Days { get; set; }

        [Column("content")]
        public String Content { get; set; }

        public Report() { }
    }
}